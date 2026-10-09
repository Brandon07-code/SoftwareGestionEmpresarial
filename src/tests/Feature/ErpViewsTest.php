<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErpViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_all_erp_modules(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@test.com')->first();
        $this->assertNotNull($admin);

        $routes = [
            '/dashboard',
            '/products',
            '/categories',
            '/citas',
            '/terceros',
            '/pagos',
            '/crm',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }
}
