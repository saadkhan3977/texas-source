<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
    
    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id')->select('orderId','id','user_id');
    }
}
