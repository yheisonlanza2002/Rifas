<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class ParticipantFactory extends Factory
{
    protected $model = \App\Models\ParticipantModel::class;

    protected $attributes = [
        'document_number' => null,
        'birth_date' => null,
        'gender' => null,
        'notes' => null,
        'source' => 'WEB',
    ];

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'company_id' => 1,
            'document_type' => $this->faker->randomElement(['CC', 'NIT', 'PASAPORTE', 'CE']),
            'document_number' => $this->faker->randomNumber(10),
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'department' => $this->faker->state,
            'status' => 'ACTIVE',
            'source' => $this->faker->randomElement(['WEB', 'POS', 'IMPORT', 'API']),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}