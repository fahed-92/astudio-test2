<?php

namespace Database\Factories;

use App\Models\OrderApproval;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderApprovalFactory extends Factory
{
    protected $model = OrderApproval::class;

    public function definition(): array
    {
        return [
            'order_id' => null, // Will be set when creating
            'approval_level' => $this->faker->randomElement(['first', 'second']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'approved_by' => $this->faker->optional()->name(),
            'notes' => $this->faker->optional()->sentence(),
            'approved_at' => $this->faker->optional()->dateTime()
        ];
    }
} 