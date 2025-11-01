<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
<<<<<<< HEAD
    protected $fillable = [
        'name', 'slug', 'description', 'status'
    ];
    public function products() {
        return $this->hasMany(Product::class);
=======
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
>>>>>>> 5b833b85b2c1795c4b56c34cd61d94684e33eca5
    }
}
