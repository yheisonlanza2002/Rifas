<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class UserFactory extends Factory
{
    protected $model = \App\Models\UserModel::class;

    protected $attributes = [
        'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4KgesMs2UpBV0LK/.byjGVQsAmQrhG', // password: secret
    ];

    protected $createRelation = false;

    protected $defaultPresets = [
        'name' => 'Test User',
        'last_name' => 'Test',
        'email' => 'test@test.com',
        'phone' => null,
        'avatar' => null,
        'timezone' => 'America/Bogota',
        'locale' => 'es_CO',
        'two_factor_enabled' => 0,
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'status' => 'ACTIVE',
        'last_login_at' => null,
    ];

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'company_id' => 1,
            'name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4KgesMs2UpBV0LK/.byjGVQsAmQrhG',
            'avatar' => null,
            'timezone' => 'America/Bogota',
            'locale' => 'es_CO',
            'two_factor_enabled' => 0,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'status' => 'ACTIVE',
            'last_login_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}