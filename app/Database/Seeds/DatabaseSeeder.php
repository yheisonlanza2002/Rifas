<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Orden por dependencias (FK)
        $this->call('CompanySeeder');
        $this->call('RolePermissionSeeder');
        $this->call('UserSeeder');
        $this->call('RaffleDemoSeeder');
        $this->call('RaffleSettingSeeder');
        $this->call('EmailTemplateSeeder');
        $this->call('PaymentGatewaySeeder');
    }
}
