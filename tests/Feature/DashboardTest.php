<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_users_are_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_pasien_users_are_redirected_to_pasien_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('pasien.dashboard'));
    }
}
