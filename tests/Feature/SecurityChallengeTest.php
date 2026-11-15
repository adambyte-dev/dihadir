<?php

namespace Tests\Feature;

use App\Http\Controllers\SecurityChallengeController;
use Tests\TestCase;

class SecurityChallengeTest extends TestCase
{
    public function test_challenge_page_renders_with_target(): void
    {
        $response = $this->get(route('security.challenge', ['r' => '/kebijakan-privasi']));

        $response->assertStatus(200);
        $response->assertSee('Memeriksa Keamanan Peramban Anda');
        $response->assertSee('DIHADIR');
        $response->assertSee('Ray ID:');
    }

    public function test_challenge_verification_issues_cookie(): void
    {
        $ip = '127.0.0.1';
        $token = SecurityChallengeController::generateToken($ip);

        $response = $this->postJson(route('security.verify'), [
            'token' => $token,
            'r' => route('landing'),
        ], ['REMOTE_ADDR' => $ip]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertCookie('cf_clearance_custom');
    }

    public function test_challenge_verification_rejects_invalid_token(): void
    {
        $response = $this->postJson(route('security.verify'), [
            'token' => 'invalid-token-tampered',
            'r' => route('landing'),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
}
