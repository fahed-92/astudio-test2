<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\OrderApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Order Service
 * 
 * Handles all business logic related to order management including creation, updates,
 * approval processes, and status history tracking.
 *
 * @author Fahed
 * @package App\Services
 */
class OrderService
{
    /**
     * @var OrderNumberGenerator
     */
    private OrderNumberGenerator $orderNumberGenerator;

    /**
     * Create a new OrderService instance.
     *
     * @author Fahed
     * @param OrderNumberGenerator $orderNumberGenerator
     */
    public function __construct(OrderNumberGenerator $orderNumberGenerator)
    {
        $this->orderNumberGenerator = $orderNumberGenerator;
    }

    /**
     * Create a new order with its items.
     *
     * @author Fahed
     * @param array $data Order data including notes
     * @param array $items Array of order items
     * @return Order
     * @throws \Exception When items array is empty
     */
    public function createOrder(array $data, array $items): Order
    {
        // Validate items array is not empty
        if (empty($items)) {
            throw new \Exception('Order must have at least one item');
        }

        return DB::transaction(function () use ($data, $items) {
            $order = Order::create([
                'order_number' => $this->orderNumberGenerator->generate(),
                'total_amount' => 0,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null
            ]);

            $totalAmount = 0;
            foreach ($items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'description' => $item['description'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity']
                ]);
                $totalAmount += $orderItem->subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);
            $this->createStatusHistory($order, 'draft', 'Order created');

            return $order;
        });
    }

    /**
     * Update an existing order and its items.
     *
     * @author Fahed
     * @param Order $order The order to update
     * @param array $data Updated order data
     * @param array $items Updated order items
     * @return Order
     * @throws \Exception When order cannot be modified or items array is empty
     */
    public function updateOrder(Order $order, array $data, array $items): Order
    {
        // Validate items array is not empty
        if (empty($items)) {
            throw new \Exception('Order must have at least one item');
        }

        if (!$order->canBeModified()) {
            throw new \Exception('Order cannot be modified after approval or rejection');
        }

        return DB::transaction(function () use ($order, $data, $items) {
            $order->update([
                'notes' => $data['notes'] ?? $order->notes
            ]);

            // Delete existing items
            $order->items()->delete();

            $totalAmount = 0;
            foreach ($items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'description' => $item['description'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity']
                ]);
                $totalAmount += $orderItem->subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);
            $this->createStatusHistory($order, 'draft', 'Order updated');

            return $order;
        });
    }

    /**
     * Submit an order for approval process.
     *
     * @author Fahed
     * @param Order $order The order to submit
     * @return Order
     * @throws \Exception When order cannot be modified
     */
    public function submitForApproval(Order $order): Order
    {
        if (!$order->canBeModified()) {
            throw new \Exception('Order cannot be modified after approval or rejection');
        }

        return DB::transaction(function () use ($order) {
            $order->update(['status' => 'pending_approval']);
            $this->createStatusHistory($order, 'pending_approval', 'Order submitted for approval');

            // Delete any existing approvals
            $order->approvals()->delete();

            // Create new approval records if required
            if ($order->requiresApproval()) {
                $this->createApprovalRecords($order);
            } else {
                // If no approval required, automatically approve
                $order->update(['status' => 'approved']);
                $this->createStatusHistory($order, 'approved', 'Order automatically approved (no approval required)');
            }

            return $order;
        });
    }

    /**
     * Process an approval for an order at a specific level.
     *
     * @author Fahed
     * @param Order $order The order to process
     * @param string $approvalLevel The level of approval (first/second)
     * @param string $status The approval status (approved/rejected)
     * @param string $approvedBy The name of the approver
     * @param string|null $notes Additional notes for the approval
     * @return Order
     * @throws \Exception When order is not pending approval or no pending approval found
     */
    public function processApproval(Order $order, string $approvalLevel, string $status, string $approvedBy, ?string $notes = null): Order
    {
        // Check if order is in pending_approval status
        if ($order->status !== 'pending_approval') {
            throw new \Exception('Order is not pending approval');
        }

        // Check if order requires approval
        if (!$order->requiresApproval()) {
            throw new \Exception('Order does not require approval');
        }

        $approval = $order->approvals()
            ->where('approval_level', $approvalLevel)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            throw new \Exception("No pending approval found for {$approvalLevel} level");
        }

        return DB::transaction(function () use ($order, $approval, $status, $approvedBy, $notes) {
            $approval->update([
                'status' => $status,
                'approved_by' => $approvedBy,
                'notes' => $notes,
                'approved_at' => now()
            ]);

            if ($status === 'rejected') {
                $order->update(['status' => 'rejected']);
                $this->createStatusHistory($order, 'rejected', "Order rejected at {$approval->approval_level} level");
            } else {
                $nextApproval = $order->approvals()
                    ->where('approval_level', 'second')
                    ->where('status', 'pending')
                    ->first();

                if (!$nextApproval) {
                    $order->update(['status' => 'approved']);
                    $this->createStatusHistory($order, 'approved', 'Order fully approved');
                }
            }

            return $order;
        });
    }

    /**
     * Create a status history record for an order.
     *
     * @author Fahed
     * @param Order $order The order
     * @param string $status The new status
     * @param string $notes Status change notes
     * @return void
     */
    private function createStatusHistory(Order $order, string $status, string $notes): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'notes' => $notes,
            'changed_by' => auth()->user()?->name ?? 'system'
        ]);
    }

    /**
     * Create approval records for an order.
     *
     * @author Fahed
     * @param Order $order The order
     * @return void
     */
    private function createApprovalRecords(Order $order): void
    {
        OrderApproval::create([
            'order_id' => $order->id,
            'approval_level' => 'first',
            'status' => 'pending'
        ]);

        OrderApproval::create([
            'order_id' => $order->id,
            'approval_level' => 'second',
            'status' => 'pending'
        ]);
    }

    /**
     * Get the complete history of an order.
     *
     * @author Fahed
     * @param Order $order The order
     * @return Collection
     */
    public function getOrderHistory(Order $order): Collection
    {
        return $order->statusHistory()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
