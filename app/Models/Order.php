<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Model
 * 
 * Represents an order in the system with its associated items, status history, and approvals.
 * Implements business rules for order management including approval requirements and modification restrictions.
 *
 * @author Fahed
 * @package App\Models
 * @property string $order_number Unique sequential order number
 * @property float $total_amount Total amount of the order
 * @property string $status Current status of the order (draft, pending_approval, approved, rejected)
 * @property string|null $notes Additional notes for the order
 * @property \Carbon\Carbon $created_at Order creation timestamp
 * @property \Carbon\Carbon $updated_at Order last update timestamp
 * @property \Carbon\Carbon|null $deleted_at Soft delete timestamp
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @author Fahed
     * @var array<string>
     */
    protected $fillable = [
        'order_number',
        'total_amount',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @author Fahed
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the items associated with the order.
     *
     * @author Fahed
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status history records for the order.
     *
     * @author Fahed
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the approval records for the order.
     *
     * @author Fahed
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(OrderApproval::class);
    }

    /**
     * Check if the order requires approval based on total amount.
     *
     * @author Fahed
     * @return bool
     */
    public function requiresApproval(): bool
    {
        return $this->total_amount >= 1000;
    }

    /**
     * Check if the order can be modified based on its current status.
     *
     * @author Fahed
     * @return bool
     */
    public function canBeModified(): bool
    {
        return !in_array($this->status, ['approved', 'rejected']);
    }

    /**
     * Boot the model.
     * Enforces business rules for order creation and modification.
     *
     * @author Fahed
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure order has at least one item
        static::saving(function ($order) {
            if ($order->items()->count() === 0) {
                throw new \Exception('Order must have at least one item');
            }
        });

        // Ensure order number is unique
        static::creating(function ($order) {
            if (static::where('order_number', $order->order_number)->exists()) {
                throw new \Exception('Order number must be unique');
            }
        });
    }
}
