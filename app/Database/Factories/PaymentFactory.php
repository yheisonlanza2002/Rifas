<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class PaymentFactory extends Factory
{
    protected $model = \App\Models\PaymentModel::class;

    protected $attributes = [
        'payment_method' => 'CARD',
        'status' => 'PENDING',
        'fee_amount' => 0.00,
        'net_amount' => 0.00,
    ];

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'company_id' => 1,
            'sale_id' => 1,
            'gateway_id' => 1,
            'amount' => $this->faker->randomNumber(1000, 50000),
            'fee_amount' => $this->faker->randomNumber(0, 500),
            'net_amount' => 0.00,
            'payment_method' => $this->randomElement(['CARD', 'BANK_TRANSFER', 'EFECTY', 'NEQUI']),
            'status' => $this->randomElement(['PENDING', 'PROCESSING', 'APPROVED']),
            'transaction_reference' => 'REF-' . $this->randomNumber(100000, 999999),
            'gateway' => 'wompi',
            'paid_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}