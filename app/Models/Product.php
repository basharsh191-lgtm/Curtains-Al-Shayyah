<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

// المنتج ينتمي لتصنيف واحد
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // المنتج يملك عدة صور
    public function images()
    {
        return $this->hasMany(Product_image::class)->orderBy('sort_order');
    }

    // المنتج يملك عدة ألوان
    public function colors()
    {
        return $this->hasMany(Product_color::class);
    }
}
