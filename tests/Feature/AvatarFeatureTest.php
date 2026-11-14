<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvatarFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_service_generates_valid_svg(): void
    {
        $svg = AvatarService::renderSvg('lorelei', 'Super Admin');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    public function test_user_avatar_endpoint_returns_svg(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'avatar_style' => 'bottts',
            'avatar_seed' => 'Budi-Seed',
        ]);

        $response = $this->get(route('avatar.user', $user));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml; charset=utf-8');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_avatar_preview_endpoint(): void
    {
        $response = $this->get(route('avatar.preview', [
            'style' => 'avataaars',
            'seed' => 'TestingPreview',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml; charset=utf-8');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_admin_can_update_avatar_style_and_seed(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'avatar_style' => 'lorelei',
            'avatar_seed' => 'initial-seed',
        ]);

        $response = $this->actingAs($user)->put(route('admin.pengaturan.profile'), [
            'name' => 'Budi Updated',
            'email' => 'budi_updated@smkn1majalengka.sch.id',
            'nip' => '198501012010011002',
            'avatar_style' => 'shapes',
            'avatar_seed' => 'Shapes-Custom-123',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index', ['tab' => 'profil']));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar_style' => 'shapes',
            'avatar_seed' => 'Shapes-Custom-123',
        ]);
    }
}
