<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'stock',
        'type_of_unit',
        'img',
        'is_dismantling',
        'created_by',
        'updated_by', 
    ];


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'products_x_orders');
    }
    
    public function dismantling(){
        return $this->hasMany(Dismantling::class, 'product_id', 'id');
    }
    public function lotes(){
        return $this->hasMany(Lot::class, 'product_id', 'id');
    }
}
