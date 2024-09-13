<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order_Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;

    /**
     * @var string
     */

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'total_price',
        'status',
        'shipping_price',
        'customer_id',
    ];
    protected $casts = [
        'status' => OrderStatus::class,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function order_product()
    {
        return $this->hasMany(Order_Product::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public static function boot()
    {

        parent::boot();
        static::saved(function ($model) {
            // Initialize the total price
            $totalPrice = 0;

            // Reload the 'products' relationship after save to ensure it's populated
            $model->load('products');

            // Loop through each product in the pivot table
            foreach ($model->products as $product) {
                $quantity = $product->pivot->quantity;
                $totalPrice += $product->price * $quantity;
                // dd($totalPrice);
                // Decrement stock for each product
                $product->decrement('quantity', $quantity);
            }

            // Add shipping price to the total price
            $totalPrice += $model->shipping_price;

            // Use updateQuietly to avoid triggering the event again
            $model->updateQuietly(['total_price' => $totalPrice]);
        });



        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity'); // Pivot table columns
    }
}
