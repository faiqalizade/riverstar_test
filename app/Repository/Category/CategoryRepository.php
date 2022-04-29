<?php

namespace App\Repository\Category;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategory($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        $category = $this->getCategory($id);

        $category->load('relations');

        if ($category->relations->count()) {
            return [
                'error' => true,
                'msg' => 'There is a product',
                'code' => 500,
            ];
        }

        if ($category->delete()) {
            return [
                'error' => false,
                'msg' => 'Successfully',
                'code' => 500,
            ];
        }

        return [
            'error' => true,
            'msg' => 'Server error',
            'code' => 500,
        ];

    }

    /**
     * @param Request $request
     * @param Request $id
     * @return array
     */
    public function update(Request $request, $id): array
    {

        $category = $this->getCategory($id);

        $category->fill($request->only(['title']));

        if ($category->save()) {
            return [
                'error' => false,
                'msg' => 'Successfully',
                'data' => $category,
                'resource' => CategoryResource::class,
                'code' => 200,
            ];
        }
        return [
            'error' => true,
            'msg' => 'Server error',
            'code' => 500,
        ];


    }

    /**
     * @param Request $request
     * @param false $with_trashed
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getCategoriesByRequest(Request $request, bool $with_trashed = false): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        if ($with_trashed) {
            $categories = Category::withTrashed();
        } else {
            $categories = Category::query();
        }

        $categories->when($request->get('title'), function ($query) use ($request) {
            $query->where('title', 'LIKE', "%{$request->get('title')}%");
        });

        return $categories->get();

    }

    /**
     * @param Request $request
     * @return array
     */

    public function create(Request $request): array
    {
        $category = new Category();

        $category->fill($request->only(['title']));

        if ($category->save()) {
            return [
                'error' => false,
                'msg' => 'Successfully',
                'data' => $category,
                'resource' => CategoryResource::class,
                'code' => 200,
            ];
        }

        return [
            'error' => true,
            'msg' => 'Server error',
            'code' => 500,
        ];


    }

}
