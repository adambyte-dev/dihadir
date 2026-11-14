<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Services\PuzzleCaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PuzzleCaptchaFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_puzzle_challenge_endpoint_returns_valid_structure(): void
    {
        $response = $this->getJson(route('puzzle.challenge'));

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'svg',
                'potonganSvg',
                'y',
                'lebar',
                'tinggi',
                'potongan',
            ]);

        $data = $response->json();
        $this->assertNotEmpty($data['token']);
        $this->assertStringContainsString('<svg', $data['svg']);
        $this->assertStringContainsString('<svg', $data['potonganSvg']);
        $this->assertGreaterThan(0, $data['y']);
    }

    public function test_puzzle_check_and_verify_with_accurate_position(): void
    {
        $service = app(PuzzleCaptchaService::class);
        $challenge = $service->challenge();

        // Cari tahu nilai X melalui unseal secara internal untuk simulasi
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('unseal');
        $method->setAccessible(true);

        $parts = explode('.', $challenge['token']);
        $data = $method->invoke($service, $parts[0]);
        $x = $data['x'];

        // 1. Check dengan posisi pas
        $validPayload = 'pz1.' . $challenge['token'] . '.' . $x;
        $checkValid = $this->postJson(route('puzzle.check'), [
            'captcha' => $validPayload,
        ]);
        $checkValid->assertOk()->assertJson(['cocok' => true]);

        // 2. Check dengan posisi meleset jauh
        $invalidPayload = 'pz1.' . $challenge['token'] . '.' . ($x + 80);
        $checkInvalid = $this->postJson(route('puzzle.check'), [
            'captcha' => $invalidPayload,
        ]);
        $checkInvalid->assertOk()->assertJson(['cocok' => false]);

        // 3. Login peserta menggunakan token puzzle yang valid
        $participant = Participant::create([
            'nip' => '198501152010011099',
            'name' => 'Peserta Uji Captcha',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'phone_number' => '081234567899',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $loginResponse = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011099',
            'password' => 'password123',
            'slider_token' => $validPayload,
        ]);

        $loginResponse->assertRedirect(route('absensi.login'));
        $this->assertAuthenticatedAs($participant, 'participant');

        // 4. Uji pencegahan Replay (Token sudah terpakai tidak boleh bisa dipakai lagi)
        auth()->guard('participant')->logout();

        $replayResponse = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011099',
            'password' => 'password123',
            'slider_token' => $validPayload,
        ]);

        $replayResponse->assertSessionHasErrors('slider_token');
        $this->assertGuest('participant');
    }

    public function test_tampered_puzzle_signature_is_rejected(): void
    {
        $service = app(PuzzleCaptchaService::class);
        $challenge = $service->challenge();

        $tamperedPayload = 'pz1.' . $challenge['token'] . 'fakeSignature.100';

        $check = $this->postJson(route('puzzle.check'), [
            'captcha' => $tamperedPayload,
        ]);

        $check->assertOk()->assertJson(['cocok' => false]);
    }
}
