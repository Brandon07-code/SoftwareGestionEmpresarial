<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_has_full_access(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $this->assertTrue($admin->hasRole('admin'));

        $response = $this->actingAs($admin)->get(route('products.index'));
        $response->assertOk();

        $response = $this->actingAs($admin)->get(route('products.create'));
        $response->assertOk();
    }

    public function test_vendedor_can_view_products_but_cannot_create(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->assertTrue($vendedor->hasRole('vendedor'));

        // Vendedor puede ver productos
        $response = $this->actingAs($vendedor)->get(route('products.index'));
        $response->assertOk();

        // Vendedor NO puede crear productos (debe dar 403 Forbidden)
        $response = $this->actingAs($vendedor)->get(route('products.create'));
        $response->assertForbidden();
    }

    public function test_almacenista_can_create_products_but_cannot_delete(): void
    {
        $almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->assertTrue($almacenista->hasRole('almacenista'));

        // Almacenista puede acceder a crear producto
        $response = $this->actingAs($almacenista)->get(route('products.create'));
        $response->assertOk();

        // Almacenista NO puede eliminar producto (debe dar 403 Forbidden)
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->actingAs($almacenista)->delete(route('products.destroy', $product));
        $response->assertForbidden();
    }
}
