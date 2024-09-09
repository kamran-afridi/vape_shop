<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class Order_Product extends Pivot
{
    use HasFactory;
    protected $table = 'order_product';
    /**
     * @var string
     */

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'category_id',
        'product_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            // Initialize the total price
            $totalPrice = 0;

            // Get all the products added in the repeater
            foreach ($model->order_product as $orderProduct) {
                $product = Product::find($orderProduct['product_id']);

                if ($product) {
                    // Calculate the price for each product (price * quantity)
                    $totalPrice += $product->price * $orderProduct['quantity'];

                    // Decrement the product quantity in stock
                    $product->decrement('quantity', $orderProduct['quantity']);
                }
            }

            // Add the shipping price to the total price
            $totalPrice += $model->shipping_price;

            // Update the total price in the model
            $model->total_price = $totalPrice;
        });
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }
    // public function products(): BelongsToMany
    // {
    //     return $this->belongsToMany(Product::class, 'order_product')
    //                 ->withPivot('quantity'); // Assuming you have a pivot table for order_product
    // }
    // public function getProductNamesAttribute()
    // {
    //     return Product::whereIn('id', $this->product_ids)
    //         ->pluck('name')
    //         ->implode(', ');
    // }
}
