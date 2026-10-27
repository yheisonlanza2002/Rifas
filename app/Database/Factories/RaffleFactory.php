<?php

namespace App\Database\Factories;

use CodeIgniter\Database\Factories\Factory;
use CodeIgniter\UUID\UUID;

class RaffleFactory extends Factory
{
    protected $model = \App\Models\RaffleModel::class;

    protected $attributes = [
        'ticket_quantity' => 10,
        'numbers_per_ticket' => 1,
        'ticket_price' => 5000.00,
        'reservation_minutes' => 15,
        'number_assignment_mode' => 'SEQUENTIAL',
        'number_selection_mode' => 'BOTH',
        'currency' => 'COP',
        'commission_rate' => 0.00,
    ];

    public function definition()
    {
        return [
            'uuid' => UUID::v4(),
            'company_id' => 1,
            'name' => $this->faker->word . ' ' . $this->faker->word,
            'description' => $this->faker->sentence,
            'code' => 'RIFAS-' . $this->randomNumber(10000, 99999),
            'status' => $this->randomElement(['DRAFT', 'SCHEDULED', 'ACTIVE']),
            'ticket_quantity' => $this->randomNumber(10, 1000),
            'numbers_per_ticket' => $this->randomElement([1, 5, 10]),
            'ticket_price' => $this->randomNumber(1000, 50000),
            'currency' => 'COP',
            'commission_rate' => $this->randomNumber(0, 20) / 100,
            'reservation_minutes' => $this->randomElement([15, 30, 60]),
            'start_at' => $this->faker->dateTimeThisDecade(),
            'end_at' => $this->faker->dateTimeNextYear(),
            'terms_conditions' => $this->faker->text(200),
            'banner_image' => $this->faker->imageUrl(800, 400),
            'gallery' => json_encode($this->faker->images(3, 800, 400)),
            'settings' => json_encode(['min_participants' => 1, 'max_participants' => 1000]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}