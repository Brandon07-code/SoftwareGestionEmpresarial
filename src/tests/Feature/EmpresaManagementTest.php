<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Empresa;
use App\Models\Product;
use App\Models\User;
use App\Services\SaleService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $cajero;

    protected User $almacenista;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
    }

    public function test_admin_can_view_empresas_index_and_see_seeded_organizations(): void
    {
        $response = $this->actingAs($this->admin)->get(route('empresas.index'));

        $response->assertOk();
        $response->assertSee('Gestión de Organizaciones');
        $response->assertSee('Barbería y Perfumería JyM');
        $response->assertSee('Comercial del Valle');
    }

    public function test_admin_can_update_empresa_profile(): void
    {
        $empresa = Empresa::first();

        $payload = [
            'nit' => '901.884.231-1',
            'razon_social' => 'Barbería & Spa JyM Internacional S.A.S.',
            'nombre_comercial' => 'JyM Barbershop & Spa',
            'tipo_negocio' => 'mixto',
            'telefono' => '3159998877',
            'email' => 'gerencia@jymbarber.co',
            'direccion' => 'Calle 10 # 5-20 Centro Histórico',
            'ciudad' => 'Cartago, Valle',
            'moneda' => 'COP',
            'activo' => '1',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('empresas.update', $empresa), $payload);

        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('empresas', [
            'id' => $empresa->id,
            'razon_social' => 'Barbería & Spa JyM Internacional S.A.S.',
            'nombre_comercial' => 'JyM Barbershop & Spa',
            'telefono' => '3159998877',
        ]);
    }

    public function test_admin_can_create_new_organization(): void
    {
        $payload = [
            'nit' => '900.776.543-9',
            'razon_social' => 'Centro Estético y Peluquería del Norte S.A.S.',
            'nombre_comercial' => 'Peluquería del Norte',
            'tipo_negocio' => 'servicios',
            'telefono' => '3201112233',
            'email' => 'admin@peluquerianorte.com',
            'direccion' => 'Avenida Bolívar # 19N-40',
            'ciudad' => 'Armenia, Quindío',
            'moneda' => 'COP',
            'activo' => '1',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('empresas.store'), $payload);

        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('empresas', [
            'nit' => '900.776.543-9',
            'nombre_comercial' => 'Peluquería del Norte',
        ]);
    }

    public function test_non_admin_users_cannot_access_or_modify_empresas(): void
    {
        $empresa = Empresa::first();

        // Cajero recibe 403 Forbidden
        $this->actingAs($this->cajero)
            ->get(route('empresas.index'))
            ->assertForbidden();

        $this->actingAs($this->cajero)
            ->get(route('empresas.edit', $empresa))
            ->assertForbidden();

        // Almacenista recibe 403 Forbidden
        $this->actingAs($this->almacenista)
            ->get(route('empresas.index'))
            ->assertForbidden();
    }

    public function test_pdf_layout_uses_active_empresa_details(): void
    {
        $client = Client::first();
        $product = Product::where('stock', '>=', 2)->first();

        $sale = app(SaleService::class)->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ], $this->admin->id);

        $empresa = Empresa::first();
        $empresa->update([
            'razon_social' => 'Corporación Comercial Turnix S.A.S.',
            'nombre_comercial' => 'Turnix Multitienda',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('sales.pdf', $sale));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
