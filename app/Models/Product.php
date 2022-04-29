<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static find($id)
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;



    const POSTED = 1;

    protected $fillable = [
        'title', 'price', 'status'
    ];
    protected $hidden = [
        'updated_at', 'pivot'
    ];




    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function relations()
    {
        return $this->hasMany(ProductCategoryRel::class);
    }

    public function getStatusAttribute(): string
    {
        if($this->getAttributes('status') == self::POSTED){
            return 'POSTED';
        }
        return 'NOT POSTED';
    }

}
