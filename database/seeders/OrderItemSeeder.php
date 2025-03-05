<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Create 1-3 items for each order
            $numberOfItems = rand(1, 3);

            for ($i = 0; $i < $numberOfItems; $i++) {
                $unitPrice = rand(50, 500);
                $quantity = rand(1, 5);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => "Product " . ($i + 1),
                    'description' => "Description for Product " . ($i + 1),
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $unitPrice * $quantity
                ]);
            }

            // Update order total amount based on items
            $totalAmount = $order->items()->sum('subtotal');
            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
