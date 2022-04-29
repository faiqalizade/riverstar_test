<?php

namespace App\Repository\Category;

use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function update(Request $request, int $id): array;

    public function create(Request $request): array;

    public function delete(int $id): array;

    public function getCategoriesByRequest(Request $request, bool $with_trashed = false): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection;

}
