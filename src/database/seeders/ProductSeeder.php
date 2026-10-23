<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener o crear categorías del dominio
        $perfumeria = Category::firstOrCreate(
            ['name' => 'Perfumería & Fragancias'],
            ['description' => 'Lociones finas y perfumería masculina de alta gama']
        );
        $cuidadoCapilar = Category::firstOrCreate(
            ['name' => 'Cuidado Capilar & Ceras'],
            ['description' => 'Ceras efecto mate, pomadas y champús profesionales']
        );
        $barberiaAfeitado = Category::firstOrCreate(
            ['name' => 'Barbería & Afeitado'],
            ['description' => 'Aceites para barba, toallas y bálsamos post-afeitado']
        );
        $maquinas = Category::firstOrCreate(
            ['name' => 'Electrónicos & Máquinas'],
            ['description' => 'Máquinas de corte, patilleras y afeitadoras']
        );
        $accesorios = Category::firstOrCreate(
            ['name' => 'Accesorios & Cuidado Personal'],
            ['description' => 'Cepillos de cerda, peines y navajas']
        );

        $products = [
            [
                'name' => 'Wahl Magic Clip Cordless',
                'description' => 'Máquina profesional inalámbrica con cuchilla fade de titanio',
                'price' => 450000.00,
                'stock' => 8,
                'category_id' => $maquinas->id,
                'active' => true,
            ],
            [
                'name' => 'Perfume Savage Eau de Parfum 100ml',
                'description' => 'Fragancia amaderada especiada de alta duración para caballero',
                'price' => 280000.00,
                'stock' => 12,
                'category_id' => $perfumeria->id,
                'active' => true,
            ],
            [
                'name' => 'Loción Bleu Night 100ml',
                'description' => 'Perfume cítrico amaderado de alta concentración',
                'price' => 110000.00,
                'stock' => 15,
                'category_id' => $perfumeria->id,
                'active' => true,
            ],
            [
                'name' => 'Cera Mate Fijación Fuerte Uppercut',
                'description' => 'Cera modeladora a base de agua sin brillo residual',
                'price' => 45000.00,
                'stock' => 25,
                'category_id' => $cuidadoCapilar->id,
                'active' => true,
            ],
            [
                'name' => 'Pomada Brillo Efecto Húmedo 150g',
                'description' => 'Pomada fijadora clásica con fragancia suave a vainilla',
                'price' => 38000.00,
                'stock' => 4, // Stock crítico (<= 5) para probar reabastecimiento en Dashboard
                'category_id' => $cuidadoCapilar->id,
                'active' => true,
            ],
            [
                'name' => 'Shampoo Anticaída con Biotina 300ml',
                'description' => 'Champú fortalecedor capilar de uso diario',
                'price' => 42000.00,
                'stock' => 10,
                'category_id' => $cuidadoCapilar->id,
                'active' => true,
            ],
            [
                'name' => 'Óleo Tónico Crecimiento Barba 30ml',
                'description' => 'Aceite esencial nutritivo con biotina, argán y eucalipto',
                'price' => 35000.00,
                'stock' => 18,
                'category_id' => $barberiaAfeitado->id,
                'active' => true,
            ],
            [
                'name' => 'Bálsamo Post-Afeitado Hidratante',
                'description' => 'Crema calmante anti-irritación con aloe vera y mentol',
                'price' => 32000.00,
                'stock' => 3, // Stock crítico (<= 5) para probar reabastecimiento en Dashboard
                'category_id' => $barberiaAfeitado->id,
                'active' => true,
            ],
            [
                'name' => 'Navaja Barbera Profesional Acero Inox',
                'description' => 'Portanavajas ergonómico de acero quirúrgico inoxidable',
                'price' => 28000.00,
                'stock' => 30,
                'category_id' => $accesorios->id,
                'active' => true,
            ],
            [
                'name' => 'Peine Fade Profesional Antiestático',
                'description' => 'Peine plano especial para degradados milimétricos',
                'price' => 18000.00,
                'stock' => 2, // Stock crítico (<= 5) para probar reabastecimiento en Dashboard
                'category_id' => $accesorios->id,
                'active' => true,
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(['name' => $prod['name']], $prod);
        }
    }
}
