<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderList;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'phone',
        'address',
        'total_price',
        'status'
    ];

    // Table relation with order lists
    public function order_list(): HasMany
    {
        return $this->hasMany(OrderList::class, 'order_code', 'order_code');
    }

}
