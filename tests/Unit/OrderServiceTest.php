<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderApproval;
use App\Services\OrderService;
use App\Services\OrderNumberGenerator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private OrderNumberGenerator $orderNumberGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderNumberGenerator = $this->mock(OrderNumberGenerator::class);
        $this->orderService = new OrderService($this->orderNumberGenerator);
    }

    public function test_order_creation_with_items()
    {
        $this->orderNumberGenerator->shouldReceive('generate')
            ->once()
            ->andReturn('ORD000001');

        $orderData = [
            'notes' => 'Test order',
            'items' => [
                [
                    'product_name' => 'Product 1',
                    'unit_price' => 100,
                    'quantity' => 2
                ]
            ]
        ];

        $order = $this->orderService->createOrder($orderData, $orderData['items']);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('ORD000001', $order->order_number);
        $this->assertEquals(200, $order->total_amount);
        $this->assertEquals('draft', $order->status);
        $this->assertCount(1, $order->items);
    }

    public function test_order_approval_workflow()
    {
        $order = Order::factory()->create([
            'total_amount' => 1500,
            'status' => 'draft'
        ]);

        // Submit for approval
        $order = $this->orderService->submitForApproval($order);
        $this->assertEquals('pending_approval', $order->status);
        $this->assertCount(2, $order->approvals);

        // First level approval
        $order = $this->orderService->processApproval(
            $order,
            'first',
            'approved',
            'Test User',
            'First level approved'
        );
        $this->assertEquals('pending_approval', $order->status);

        // Second level approval
        $order = $this->orderService->processApproval(
            $order,
            'second',
            'approved',
            'Test User',
            'Second level approved'
        );
        $this->assertEquals('approved', $order->status);
    }

    public function test_order_rejection()
    {
        $order = Order::factory()->create([
            'total_amount' => 1500,
            'status' => 'draft'
        ]);

        // Submit for approval
        $order = $this->orderService->submitForApproval($order);

        // Reject at first level
        $order = $this->orderService->processApproval(
            $order,
            'first',
            'rejected',
            'Test User',
            'Rejected at first level'
        );
        $this->assertEquals('rejected', $order->status);
    }

    public function test_order_update_validation()
    {
        $order = Order::factory()->create([
            'status' => 'approved'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order cannot be modified after approval');

        $this->orderService->updateOrder($order, [], []);
    }
} 