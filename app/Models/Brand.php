<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'status',
    ];

    /**
     * Quan hệ với Product
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
