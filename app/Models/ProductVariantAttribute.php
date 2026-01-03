<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}
