<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static find($id)
 */

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['products_count'];
    protected $fillable = ['title'];


    protected $hidden = [
        'updated_at', 'pivot'
    ];

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function relations(){
        return $this->hasMany(ProductCategoryRel::class);
    }

    public function getProductsCountAttribute(){
        return $this->products()->count();
    }
}
