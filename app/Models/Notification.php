<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['message','order_id', 'product_id', 'read', 'type'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
