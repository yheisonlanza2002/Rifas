<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Evita duplicados si ya existe la empresa por slug
        $exists = $db->table('companies')->where('slug', 'rifas-demo')->countAllResults();
        if ($exists > 0) {
            return;
        }

        $db->table('companies')->insert([
            'uuid'           => $this->generateUuid(),
            'name'           => 'Rifas Demo',
            'slug'           => 'rifas-demo',
            'legal_name'     => 'Rifas Demo S.A.S.',
            'document_type'  => 'NIT',
            'document_number' => '900123456-1',
            'email'          => 'contacto@rifasdemo.com',
            'phone'          => '3001234567',
            'address'        => 'Calle 100 # 10-20',
            'city'           => 'Bogotá',
            'department'     => 'Cundinamarca',
            'country'        => 'Colombia',
            'settings'       => json_encode(['timezone' => 'America/Bogota', 'locale' => 'es_CO']),
            'status'         => 'ACTIVE',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
