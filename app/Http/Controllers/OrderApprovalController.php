<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\OrderApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Order Approval Controller
 * 
 * Handles all HTTP requests related to order approval process including
 * processing approvals at different levels and retrieving pending approvals.
 *
 * @author Fahed
 * @package App\Http\Controllers
 */
class OrderApprovalController extends Controller
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * Create a new OrderApprovalController instance.
     *
     * @author Fahed
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Process an approval for an order.
     *
     * @author Fahed
     * @param OrderApprovalRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function processApproval(OrderApprovalRequest $request, Order $order): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $order = $this->orderService->processApproval(
            $order,
            $request->input('approval_level'),
            $request->input('status'),
            Auth::user()->name,
            $request->input('notes')
        );

        return response()->json(['data' => $order]);
    }

    /**
     * Get all pending approvals for the authenticated user.
     *
     * @author Fahed
     * @return JsonResponse
     */
    public function pendingApprovals(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $pendingApprovals = Order::with(['items', 'approvals'])
            ->whereHas('approvals', function ($query) {
                $query->where('status', 'pending');
            })
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($pendingApprovals);
    }
}
