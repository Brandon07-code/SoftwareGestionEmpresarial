<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'document_type' => 'NIT',
                'document_number' => '900887654-2',
                'name' => 'Distribuidora Cosméticos del Eje S.A.S.',
                'contact_name' => 'Diana Marcela Mejía',
                'email' => 'ventas@cosmeticosdeleje.com',
                'phone' => '3206654433',
                'address' => 'Dosquebradas / Cartago, Av. Simón Bolívar #18-20',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '901334455-8',
                'name' => 'Laboratorios Barber Tools Colombia S.A.S.',
                'contact_name' => 'Camilo Echeverry',
                'email' => 'comercial@barbertools.co',
                'phone' => '6044567890',
                'address' => 'Medellín, Carrera 43A #1-50',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '900555777-1',
                'name' => 'Perfumería & Fragancias de Occidente S.A.S.',
                'contact_name' => 'Laura Sofía Ríos',
                'email' => 'pedidos@fraganciasoccidente.com',
                'phone' => '6027654321',
                'address' => 'Cali, Av. 6N #23-45',
                'active' => true,
            ],
            [
                'document_type' => 'CC',
                'document_number' => '1094555666',
                'name' => 'Insumos Barber & Spa Cartago (Andrés Cardona)',
                'contact_name' => 'Andrés Cardona',
                'email' => 'andres.cardona@barberspa.test',
                'phone' => '3135556677',
                'address' => 'Cartago, Calle 12 #5-30',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '900222111-4',
                'name' => 'Droguería y Cosmética del Valle S.A.S.',
                'contact_name' => 'Pedro Salazar',
                'email' => 'contacto@cosmeticadelvalle.com',
                'phone' => '6022149988',
                'address' => 'Cartago, Carrera 4 #10-15',
                'active' => true,
            ],
        ];

        foreach ($providers as $provider) {
            Provider::firstOrCreate(['document_number' => $provider['document_number']], $provider);
        }
    }
}
