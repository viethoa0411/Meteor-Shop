<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','slug','description','price','stock','image','category_id','brand_id','status'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function variants() {
        return $this->hasMany(ProductVariant::class)
    }
}
