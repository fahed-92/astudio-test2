<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Order Number Generator Service
 * 
 * Generates unique and sequential order numbers with a predefined prefix and padding.
 * Ensures thread-safe generation of order numbers using database transactions.
 *
 * @author Fahed
 * @package App\Services
 */
class OrderNumberGenerator
{
    /**
     * The prefix to use for order numbers
     *
     * @author Fahed
     * @var string
     */
    private const PREFIX = 'ORD';

    /**
     * The number of digits to pad the order number with
     *
     * @author Fahed
     * @var int
     */
    private const PADDING = 6;

    /**
     * Generate a new unique and sequential order number.
     * 
     * This method uses a database transaction to ensure thread-safe generation
     * of order numbers. It extracts the numeric part from the last order number
     * and increments it by 1, then formats it with the prefix and padding.
     *
     * @author Fahed
     * @return string The generated order number
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            // Get the last order number
            $lastOrder = Order::orderBy('id', 'desc')->first();
            
            // Extract the numeric part from the last order number or start from 1
            $lastNumber = $lastOrder ? (int)substr($lastOrder->order_number, strlen(self::PREFIX)) : 0;
            
            // Generate the next number
            $nextNumber = $lastNumber + 1;
            
            // Format the order number with prefix and padding
            return self::PREFIX . str_pad($nextNumber, self::PADDING, '0', STR_PAD_LEFT);
        });
    }
}
