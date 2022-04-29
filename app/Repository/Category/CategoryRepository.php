<?php

namespace App\Repository\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategory($id)
    {
        return Category::find($id);
    }

    public function delete($id)
    {
        try {
            $category = $this->getCategory($id);

            if(!$category){
                return [
                    'error' => true,
                    'msg' => 'Category not found',
                    'code' => 404,
                ];
            }

            $category->load('relations');

            if ($category->relations->count()) {
                return [
                    'error' => true,
                    'msg' => 'There is a product',
                    'code' => 500,
                ];
            }

            $category->delete();

            return [
                'error' => false,
                'msg' => 'Successfully',
                'code' => 500,
            ];


        } catch (\Exception $exception) {
            return [
                'error' => true,
                'msg' => $exception->getMessage(),
                'code' => 500,
            ];
        }

    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->getCategory($id);
            if (!$category) {
                return [
                    'error' => true,
                    'msg' => 'Category not found',
                    'code' => 404,
                ];
            }
            $category->fill($request->only(['title']));

            $saved = $category->save();


            if ($saved) {
                DB::commit();

                return [
                    'error' => false,
                    'msg' => 'Successfully',
                    'data' => $category,
                    'code' => 200,
                ];
            }

            return [
                'error' => true,
                'msg' => 'Server error',
                'code' => 500,
            ];


        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'error' => true,
                'msg' => $exception->getMessage(),
                'code' => 500,
            ];
        }

    }

    public function getCategoriesByRequest(Request $request, $with_trashed = false)
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

    public function create(Request $request)
    {
        try {
            $category = new Category();

            $category->fill($request->only(['title']));

            $saved = $category->save();


            if ($saved) {
                return [
                    'error' => false,
                    'msg' => 'Successfully',
                    'data' => $category,
                    'code' => 200,
                ];
            }
            return [
                'error' => true,
                'msg' => 'Server error',
                'code' => 500,
            ];

        } catch (\Exception $exception) {

            return [
                'error' => true,
                'msg' => $exception->getMessage(),
                'code' => 200,
            ];

        }
    }

}
