<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_can_be_modified_when_not_approved()
    {
        $order = Order::factory()->create(['status' => 'draft']);
        $this->assertTrue($order->canBeModified());

        $order->update(['status' => 'pending_approval']);
        $this->assertTrue($order->canBeModified());

        $order->update(['status' => 'rejected']);
        $this->assertTrue($order->canBeModified());

        $order->update(['status' => 'approved']);
        $this->assertFalse($order->canBeModified());
    }

    public function test_order_requires_approval_when_total_exceeds_threshold()
    {
        $order = Order::factory()->create(['total_amount' => 999.99]);
        $this->assertFalse($order->requiresApproval());

        $order->update(['total_amount' => 1000.01]);
        $this->assertTrue($order->requiresApproval());
    }

    public function test_order_total_is_calculated_correctly()
    {
        $order = Order::factory()->create();
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'unit_price' => 100,
            'quantity' => 2
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'unit_price' => 50,
            'quantity' => 3
        ]);

        $this->assertEquals(350, $order->total_amount);
    }
} 