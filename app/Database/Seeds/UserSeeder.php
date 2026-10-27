<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $companyId = $db->table('companies')->where('slug', 'rifas-demo')->get()->getRow('id');

        $users = [
            [
                'email'    => 'admin@rifas.com',
                'password' => 'admin123',
                'name'     => 'Administrador',
                'last_name' => 'Sistema',
                'role'     => 'SUPER_ADMIN',
            ],
            [
                'email'    => 'vendedor@rifas.com',
                'password' => 'vendedor123',
                'name'     => 'Vendedor',
                'last_name' => 'Demo',
                'role'     => 'SELLER',
            ],
        ];

        foreach ($users as $u) {
            $exists = $db->table('users')->where('email', $u['email'])->countAllResults();
            if ($exists > 0) {
                continue;
            }

            $db->table('users')->insert([
                'uuid'          => $this->generateUuid(),
                'company_id'    => $companyId,
                'name'          => $u['name'],
                'last_name'     => $u['last_name'],
                'email'         => $u['email'],
                'phone'         => '3000000000',
                'password_hash' => password_hash($u['password'], PASSWORD_DEFAULT),
                'timezone'      => 'America/Bogota',
                'locale'        => 'es_CO',
                'status'        => 'ACTIVE',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $userId = $db->insertID();

            $roleId = $db->table('roles')->where('name', $u['role'])->get()->getRow('id');
            if ($roleId) {
                $db->table('user_roles')->insert([
                    'user_id'    => $userId,
                    'role_id'    => $roleId,
                    'created_at' => $now,
                ]);
            }
        }
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
