<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear al menos 5 categorías (cumple práctica guiada y mínimo 5 registros)
        $cat1 = Category::create(['name' => 'Electrónicos & Máquinas', 'description' => 'Máquinas de corte, patilleras y afeitadoras']);
        $cat2 = Category::create(['name' => 'Perfumería & Fragancias', 'description' => 'Lociones finas y perfumería masculina']);
        $cat3 = Category::create(['name' => 'Cuidado Capilar & Ceras', 'description' => 'Ceras efecto mate, pomadas y champús']);
        $cat4 = Category::create(['name' => 'Barbería & Afeitado', 'description' => 'Aceites para barba, toallas y bálsamos']);
        $cat5 = Category::create(['name' => 'Accesorios & Cuidado Personal', 'description' => 'Cepillos de cerda, peines y navajas']);

        // 2. Crear al menos 5 productos asociados por relación 1:N
        Product::create([
            'name' => 'Wahl Magic Clip Cordless',
            'description' => 'Máquina profesional inalámbrica con cuchilla fade',
            'price' => 450000.00,
            'stock' => 8,
            'category_id' => $cat1->id,
            'active' => true,
        ]);

        Product::create([
            'name' => 'Perfume Savage Eau de Parfum 100ml',
            'description' => 'Fragancia amaderada especiada de alta duración',
            'price' => 280000.00,
            'stock' => 12,
            'category_id' => $cat2->id,
            'active' => true,
        ]);

        Product::create([
            'name' => 'Cera Mate Fijación Fuerte Uppercut',
            'description' => 'Cera modeladora a base de agua sin brillo residual',
            'price' => 45000.00,
            'stock' => 25,
            'category_id' => $cat3->id,
            'active' => true,
        ]);

        Product::create([
            'name' => 'Óleo Tónico Crecimiento Barba 30ml',
            'description' => 'Aceite esencial nutritivo con biotina y argán',
            'price' => 35000.00,
            'stock' => 18,
            'category_id' => $cat4->id,
            'active' => true,
        ]);

        Product::create([
            'name' => 'Navaja Barbera Profesional Acero Inox',
            'description' => 'Portanavajas ergonómico de acero quirúrgico',
            'price' => 28000.00,
            'stock' => 30,
            'category_id' => $cat5->id,
            'active' => true,
        ]);
    }
}
