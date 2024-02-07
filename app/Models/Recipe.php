<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'preparation',
        'person_count',
        'type',
        'ingredients',
        'total_time',
        'image_url',
        'video_url',
        'created_by',

    ];
    public function chef()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_x_recipes')->withPivot(['quantity']);
    }
}
