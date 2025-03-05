<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $quantity = $this->faker->numberBetween(1, 10);

        return [
            'order_id' => null, // Will be set when creating
            'product_name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $unitPrice * $quantity
        ];
    }
} 