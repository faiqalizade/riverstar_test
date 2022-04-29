<?php

namespace App\Repository\Product;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function getProduct(int $id);

    public function getProductsByRequest(Request $request, $with_trashed): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection;

    public function update(Request $request, int $id): array;

    public function create(Request $request): array;

    public function delete(int $id): array;
}
