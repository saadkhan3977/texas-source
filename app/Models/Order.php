<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function product_image()
    {
        return $this->hasMany(ProductImage::class,'product_id');
    }

    public function user()
    {
        return $this->hasOne(user::class,'id','user_id');
    }
}
