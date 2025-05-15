<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'in_stock',
        'category',
        'latitude',
        'longitude',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'in_stock' => 'boolean',
    ];

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
