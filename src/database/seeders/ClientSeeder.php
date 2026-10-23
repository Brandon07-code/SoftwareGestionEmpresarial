<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'document_type' => 'CC',
                'document_number' => '1114771229',
                'name' => 'Sebastián Rivera',
                'email' => 'sebastian.rivera@test.com',
                'phone' => '3171122334',
                'address' => 'Cartago, Calle 10 #4-25',
                'active' => true,
            ],
            [
                'document_type' => 'CC',
                'document_number' => '1115342901',
                'name' => 'Juan David Henao',
                'email' => 'juan.henao@test.com',
                'phone' => '3168889900',
                'address' => 'Cartago, Carrera 6 #14-50',
                'active' => true,
            ],
            [
                'document_type' => 'CC',
                'document_number' => '1114445566',
                'name' => 'Mateo Gómez Pérez',
                'email' => 'mateo.gomez@test.com',
                'phone' => '3114445566',
                'address' => 'Cartago, Carrera 3 #8-12',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '901555444-1',
                'name' => 'Club Social & Spa Los Samanes S.A.S.',
                'email' => 'administracion@lossamanes.com',
                'phone' => '6022143322',
                'address' => 'Cartago, Av. Los Samanes #20-10',
                'active' => true,
            ],
            [
                'document_type' => 'CC',
                'document_number' => '1112450890',
                'name' => 'Carlos Alberto Duque',
                'email' => 'carlos.duque@test.com',
                'phone' => '3145550011',
                'address' => 'Cartago, Calle 15 #7-40',
                'active' => true,
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(['document_number' => $client['document_number']], $client);
        }
    }
}
