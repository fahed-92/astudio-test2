<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Order Controller
 * 
 * Handles all HTTP requests related to order management including creation,
 * updates, retrieval, and submission for approval.
 *
 * @author Fahed
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * Create a new OrderController instance.
     *
     * @author Fahed
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     *
     * @author Fahed
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $orders = Order::with(['items', 'statusHistory', 'approvals'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Store a newly created order.
     *
     * @author Fahed
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated(), $request->input('items'));

        return response()->json(['data' => $order], 201);
    }

    /**
     * Display the specified order.
     *
     * @author Fahed
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['items', 'statusHistory', 'approvals']);
        return response()->json(['data' => $order]);
    }

    /**
     * Update the specified order.
     *
     * @author Fahed
     * @param OrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->orderService->updateOrder($order, $request->validated(), $request->input('items'));

        return response()->json(['data' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Submit an order for approval.
     *
     * @author Fahed
     * @param Order $order
     * @return JsonResponse
     */
    public function submitForApproval(Order $order): JsonResponse
    {
        $order = $this->orderService->submitForApproval($order);
        return response()->json(['data' => $order]);
    }

    /**
     * Get the history of an order.
     *
     * @author Fahed
     * @param Order $order
     * @return JsonResponse
     */
    public function history(Order $order): JsonResponse
    {
        $history = $this->orderService->getOrderHistory($order);
        return response()->json(['data' => $history]);
    }
}
