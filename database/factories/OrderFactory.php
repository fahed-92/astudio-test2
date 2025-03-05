<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'total_amount' => $this->faker->randomFloat(2, 10, 5000),
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved', 'rejected']),
            'notes' => $this->faker->optional()->sentence()
        ];
    }
} 