<?php

namespace Database\Factories;

use App\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    public function definition(): array
    {
        return [
            'order_id' => null, // Will be set when creating
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved', 'rejected']),
            'notes' => $this->faker->optional()->sentence(),
            'changed_by' => $this->faker->name()
        ];
    }
} 