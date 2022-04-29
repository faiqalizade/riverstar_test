<?php

namespace App\Repository\Product;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function getProduct(int $id);
    public function getProductsByRequest(\Illuminate\Http\Request $request,$with_trashed);
    public function update(int $id,Request $request);
}
