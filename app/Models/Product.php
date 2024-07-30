<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'quantity',
        'price',
    ];

    public function orders(): BelongsToMany

    {
        return $this->belongsToMany(Order::class);
        // return $this->belongsToMany(Order::class, 'order_product')
        //     ->withPivot('quantity');
    }
    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name');
    }
    public function order_product()

    {
        return $this->hasMany(Order_Product::class, 'product_id', 'id');
        // return $this->belongsToMany(Order::class, 'order_product')
        //     ->withPivot('quantity');
    }
}
