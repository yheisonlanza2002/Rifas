<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class CompanyFactory extends Factory
{
    protected $model = \App\Models\CompanyModel::class;

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'name' => $this->faker->company,
            'legal_name' => $this->faker->company . ' Ltda.',
            'document_type' => 'RUC',
            'document_number' => $this->faker->randomNumber(10),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'country' => 'Colombia',
            'status' => 'ACTIVE',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}