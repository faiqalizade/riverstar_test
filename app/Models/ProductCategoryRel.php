<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategoryRel extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_product';

    protected $hidden = [
        'updated_at', 'pivot'
    ];


    public function categories(){
        return $this->hasMany(Category::class,'id','fk_product');
    }


}
