<?php

namespace Tests\Feature;

use App\Models\Kardex;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExcelImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $almacenista;

    protected User $cajero;

    protected User $auditor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->cajero = User::where('email', 'cajero@test.com')->first();
        $this->auditor = User::where('email', 'auditor@test.com')->first();
    }

    public function test_product_export_downloads_excel_file(): void
    {
        $response = $this->actingAs($this->admin)->get(route('products.export.excel'));

        $response->assertOk();
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('turnix_productos_', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }

    public function test_product_template_downloads_excel_file(): void
    {
        $response = $this->actingAs($this->almacenista)->get(route('products.import.template'));

        $response->assertOk();
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('plantilla_importacion_productos.xlsx', $contentDisposition);
    }

    public function test_sale_export_downloads_excel_file(): void
    {
        $response = $this->actingAs($this->cajero)->get(route('sales.export.excel'));

        $response->assertOk();
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('turnix_reporte_ventas_', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }

    public function test_product_mass_import_creates_products_and_kardex(): void
    {
        $csvContent = implode("\n", [
            'nombre,descripcion,precio,stock,categoria',
            'Cera Mate Barber JyM,Cera fijadora acabado opaco 100g,35000,15,Fijadores Capilares',
            'Peine Degradado Profesional,Peine guia para corte fade milimetrico,12500,25,Herramientas',
        ]);

        $file = UploadedFile::fake()->createWithContent('productos_carga.csv', $csvContent);

        $response = $this->actingAs($this->almacenista)->post(route('products.import.excel'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        // Verificar creación de categoría dinámica
        $this->assertDatabaseHas('categories', [
            'name' => 'Fijadores Capilares',
        ]);

        // Verificar creación de productos
        $cera = Product::where('name', 'Cera Mate Barber JyM')->first();
        $this->assertNotNull($cera);
        $this->assertEquals(35000.00, (float) $cera->price);
        $this->assertEquals(15, $cera->stock);

        $peine = Product::where('name', 'Peine Degradado Profesional')->first();
        $this->assertNotNull($peine);
        $this->assertEquals(25, $peine->stock);

        // Verificar registro de Kárdex transaccional inicial
        $kardexCera = Kardex::where('product_id', $cera->id)->where('movement_type', 'AJUSTE_INICIAL')->first();
        $this->assertNotNull($kardexCera);
        $this->assertEquals(15, $kardexCera->quantity);
        $this->assertEquals(0, $kardexCera->stock_before);
        $this->assertEquals(15, $kardexCera->stock_after);
        $this->assertEquals('IMPORT-EXCEL', $kardexCera->document_reference);
    }

    public function test_product_mass_import_validates_row_by_row_and_reports_errors(): void
    {
        $existingProduct = Product::first();

        $csvContent = implode("\n", [
            'nombre,descripcion,precio,stock,categoria',
            'Aceite Barba Eucalipto JyM,Tonico hidratante barba 30ml,28000,10,Cuidado Facial',
            ',Sin nombre no deberia pasar,15000,5,Accesorios',
            'Shampoo Invigorante,Precio negativo no permitido,-12000,8,Lavado',
            "{$existingProduct->name},Nombre duplicado no permitido,45000,6,Duplicados",
        ]);

        $file = UploadedFile::fake()->createWithContent('productos_inconsistencias.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('products.import.excel'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('warning');
        $response->assertSessionHas('import_failures');

        // El producto válido sí debió insertarse
        $this->assertDatabaseHas('products', [
            'name' => 'Aceite Barba Eucalipto JyM',
            'stock' => 10,
        ]);

        // El reporte de fallas debe contener las 3 filas defectuosas
        $failures = session('import_failures');
        $this->assertNotEmpty($failures);
        $this->assertGreaterThanOrEqual(3, count($failures));

        // Comprobar que en la vista se renderiza el reporte
        $viewResponse = $this->actingAs($this->admin)->get(route('products.index'));
        $viewResponse->assertOk();
    }

    public function test_role_permissions_for_excel_import_and_export(): void
    {
        $csvContent = "nombre,descripcion,precio,stock,categoria\nTest,Desc,1000,1,General";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        // 1. Cajero NO puede importar productos (403 Forbidden)
        $this->actingAs($this->cajero)
            ->post(route('products.import.excel'), ['file' => $file])
            ->assertForbidden();

        // 2. Auditor NO puede importar productos (403 Forbidden)
        $this->actingAs($this->auditor)
            ->post(route('products.import.excel'), ['file' => $file])
            ->assertForbidden();

        // 3. Auditor SÍ puede exportar productos y ventas (solo lectura)
        $this->actingAs($this->auditor)
            ->get(route('products.export.excel'))
            ->assertOk();

        $this->actingAs($this->auditor)
            ->get(route('sales.export.excel'))
            ->assertOk();
    }
}
