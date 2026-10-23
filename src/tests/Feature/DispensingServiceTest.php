<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Lot;
use App\Services\DispensingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class DispensingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispenses_using_fefo_and_ignores_expired_lots()
    {
        $category = Category::create(['name' => 'Medicamentos']);
        $product = Product::create([
            'name' => 'Amoxicilina 500mg',
            'category_id' => $category->id,
            'price' => 15000,
            'stock' => 30,
            'active' => true,
        ]);

        // Lot 1: Expired yesterday (10 units) -> MUST BE IGNORED
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'L1',
            'expiration_date' => now()->subDay()->toDateString(),
            'quantity' => 10,
        ]);

        // Lot 2: Expires in 1 month (5 units) -> FIRST TO BE DISPENSED
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'L2',
            'expiration_date' => now()->addMonth()->toDateString(),
            'quantity' => 5,
        ]);

        // Lot 3: Expires in 1 year (15 units) -> SECOND TO BE DISPENSED
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'L3',
            'expiration_date' => now()->addYear()->toDateString(),
            'quantity' => 15,
        ]);

        $service = app(DispensingService::class);
        $result = $service->dispense($product, 8);

        // Assertions:
        // L1 must remain intact with 10 units
        $this->assertEquals(10, Lot::where('lot_number', 'L1')->value('quantity'));

        // L2 (5 units) should be fully depleted (0 remaining)
        $this->assertEquals(0, Lot::where('lot_number', 'L2')->value('quantity'));

        // L3 (15 units) should have 3 deducted -> 12 remaining
        $this->assertEquals(12, Lot::where('lot_number', 'L3')->value('quantity'));

        // Product total stock must decrement by 8 -> 30 - 8 = 22
        $this->assertEquals(22, $product->fresh()->stock);
    }

    public function test_it_throws_exception_if_unexpired_stock_is_insufficient()
    {
        $category = Category::create(['name' => 'Analgésicos']);
        $product = Product::create([
            'name' => 'Ibuprofeno 800mg',
            'category_id' => $category->id,
            'price' => 8000,
            'stock' => 15,
            'active' => true,
        ]);

        // Expired lot with 10 units
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'EXP-01',
            'expiration_date' => now()->subDays(5)->toDateString(),
            'quantity' => 10,
        ]);

        // Valid lot with only 3 units
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'VAL-01',
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'quantity' => 3,
        ]);

        $this->expectException(ValidationException::class);

        $service = app(DispensingService::class);
        // Requesting 5 units when only 3 unexpired units exist
        $service->dispense($product, 5);
    }
}
