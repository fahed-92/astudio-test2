<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderApproval;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::where('total_amount', '>', 1000)->get();

        foreach ($orders as $order) {
            // Create first level approval
            OrderApproval::factory()->create([
                'order_id' => $order->id,
                'approval_level' => 'first',
                'status' => $order->status === 'approved' ? 'approved' : 
                           ($order->status === 'rejected' ? 'rejected' : 'pending'),
                'approved_by' => $order->status !== 'pending_approval' ? 'Test Approver' : null,
                'approved_at' => $order->status !== 'pending_approval' ? now() : null
            ]);

            // Create second level approval
            OrderApproval::factory()->create([
                'order_id' => $order->id,
                'approval_level' => 'second',
                'status' => $order->status === 'approved' ? 'approved' : 
                           ($order->status === 'rejected' ? 'rejected' : 'pending'),
                'approved_by' => $order->status === 'approved' ? 'Test Approver' : null,
                'approved_at' => $order->status === 'approved' ? now() : null
            ]);
        }
    }
}
