<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RaffleDemoSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $companyId = $db->table('companies')->where('slug', 'rifas-demo')->get()->getRow('id');
        if (!$companyId) {
            return;
        }

        $exists = $db->table('raffles')->where('code', 'RIFA-0001')->where('company_id', $companyId)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $db->table('raffles')->insert([
            'uuid'                   => $this->generateUuid(),
            'company_id'             => $companyId,
            'name'                   => 'Rifa Demo #1',
            'description'            => 'Rifa de demostración con 1000 boletas.',
            'code'                   => 'RIFA-0001',
            'status'                 => 'DRAFT',
            'ticket_quantity'        => 1000,
            'numbers_per_ticket'     => 1,
            'number_start'           => '0000',
            'number_end'             => '0999',
            'number_length'          => 4,
            'number_assignment_mode' => 'SEQUENTIAL',
            'number_selection_mode'  => 'BOTH',
            'ticket_price'           => 10000.00,
            'currency'               => 'COP',
            'commission_rate'        => 0.00,
            'reservation_minutes'    => 15,
            'terms_conditions'       => 'Términos de la rifa demo.',
            'banner_image'           => null,
            'gallery'                => null,
            'settings'               => null,
            'start_at'               => $now,
            'end_at'                 => null,
            'created_at'             => $now,
            'updated_at'             => $now,
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
