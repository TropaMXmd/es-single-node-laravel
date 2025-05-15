<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = [
        'product_id',
        'attribute_name',
        'attribute_value',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
