<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Customer;
use App\Models\Region;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['description' => 'Región Metropolitana de Santiago', 'status' => 'A'],
            ['description' => 'Región de Valparaíso', 'status' => 'A'],
            ['description' => 'Región del Biobío', 'status' => 'A'],
            ['description' => 'Región de la Araucanía', 'status' => 'A'],
            ['description' => 'Región de Los Lagos', 'status' => 'A'],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }

        $communes = [
            ['id_reg' => 1, 'description' => 'Santiago', 'status' => 'A'],
            ['id_reg' => 1, 'description' => 'Providencia', 'status' => 'A'],
            ['id_reg' => 1, 'description' => 'Las Condes', 'status' => 'A'],
            ['id_reg' => 2, 'description' => 'Valparaíso', 'status' => 'A'],
            ['id_reg' => 2, 'description' => 'Viña del Mar', 'status' => 'A'],
            ['id_reg' => 3, 'description' => 'Concepción', 'status' => 'A'],
            ['id_reg' => 4, 'description' => 'Temuco', 'status' => 'A'],
            ['id_reg' => 5, 'description' => 'Puerto Montt', 'status' => 'A'],
        ];

        foreach ($communes as $commune) {
            Commune::create($commune);
        }

        Customer::create([
            'dni' => '12345678',
            'id_reg' => 1,
            'id_com' => 1,
            'email' => 'cliente@test.com',
            'password' => '123456',
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'address' => 'Av. Principal 123',
            'date_reg' => now()->format('Y-m-d H:i:s'),
            'status' => 'A',
        ]);

        Customer::create([
            'dni' => '87654321',
            'id_reg' => 2,
            'id_com' => 4,
            'email' => 'maria@test.com',
            'password' => '123456',
            'name' => 'María',
            'last_name' => 'González',
            'address' => 'Calle Puerto 456',
            'date_reg' => now()->format('Y-m-d H:i:s'),
            'status' => 'A',
        ]);

        Customer::create([
            'dni' => '11223344',
            'id_reg' => 3,
            'id_com' => 6,
            'email' => 'pedro@test.com',
            'password' => '123456',
            'name' => 'Pedro',
            'last_name' => 'Soto',
            'address' => null,
            'date_reg' => now()->format('Y-m-d H:i:s'),
            'status' => 'I',
        ]);
    }
}
