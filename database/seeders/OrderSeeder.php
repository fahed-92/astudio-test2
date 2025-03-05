<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some draft orders
        Order::factory(5)->create([
            'status' => 'draft',
            'total_amount' => 500
        ]);

        // Create some pending approval orders
        Order::factory(3)->create([
            'status' => 'pending_approval',
            'total_amount' => 1500
        ]);

        // Create some approved orders
        Order::factory(2)->create([
            'status' => 'approved',
            'total_amount' => 2000
        ]);

        // Create some rejected orders
        Order::factory(2)->create([
            'status' => 'rejected',
            'total_amount' => 1200
        ]);
    }
}
