<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductCategoryRel;

class ProductObserve
{

    public function deleted(Product $product)
    {
        ProductCategoryRel::where('product_id',$product->id)->delete();
    }

    public function restored(Product $product)
    {
        ProductCategoryRel::where('product_id',$product->id)->restore();
    }

}
