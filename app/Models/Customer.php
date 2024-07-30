<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'shop',
        'due',
        'address',
    ];

    public function order()
    {
        return $this->hasMany(Order::class, 'id', 'customer_id');
    }
    // public function customer()
    // {
    //     return $this->hasMany(Customer::class)
    //         ->select('id', 'name');
    // }
}
