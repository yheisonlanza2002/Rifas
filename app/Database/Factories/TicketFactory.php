<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class TicketFactory extends Factory
{
    protected $model = \App\Models\TicketModel::class;

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'company_id' => 1,
            'raffle_id' => 1,
            'code' => 'B-' . $this->randomNumber(100000, 999999),
            'serial_prefix' => 'R-',
            'status' => $this->randomElement(['AVAILABLE', 'RESERVED']),
            'price' => 5000.00,
            'number_quantity' => 1,
            'bundle_quantity' => 1,
            'reserved_at' => null,
            'reservation_expires_at' => null,
            'sold_at' => null,
            'cancelled_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}