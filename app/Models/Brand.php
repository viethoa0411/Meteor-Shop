<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Brand extends Model
{
<<<<<<< HEAD
=======
    protected $table = 'brands';
>>>>>>> quan_ly_products
    protected $fillable = [
        'name', 'slug', 'description', 'status'
    ];
    public function products() {
        return $this->hasMany(Product::class);
    }
}
