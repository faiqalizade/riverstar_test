<?php

namespace App\Repository\Product;

use App\Models\Product;
use App\Models\ProductCategoryRel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{

    public function getProduct(int $id)
    {
        return Product::find($id);
    }

    private function insertRelations($categories, $product)
    {
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'product_id' => $product->id,
                'category_id' => $category,
            ];
        }

        return ProductCategoryRel::upsert($data, ['id']);
    }


    public function update(int $id, Request $request)
    {
        try {
            DB::beginTransaction();

            $product = $this->getProduct($id);

            if (!$product) {
                return [
                    'error' => 'true',
                    'msg' => 'Product not found',
                    'code' => 404,
                ];
            }

            $product->fill($request->only(['title', 'price', 'status']));

            $saved = $product->save();

            if (!$saved) {
                return [
                    'error' => 'true',
                    'msg' => '',
                    'code' => 500,
                ];
            }


            $product->load('relations');

            $rel_ids = $product->relations->pluck('category_id')->toArray();

            $delete_ids = array_diff($rel_ids, $request->get('categories')); // silinen

            ProductCategoryRel::whereIn('category_id', $delete_ids)
                ->where('product_id', $product->id)
                ->forceDelete();

            $categories = array_diff($request->get('categories'), $rel_ids); //  yeni elave olunan

            $this->insertRelations($categories, $product);

            DB::commit();

            $product->load('categories');

            unset($product->relations);

            return [
                'error' => false,
                'msg' => 'Successfully',
                'data' => $product,
            ];

        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'error' => 'true',
                'msg' => $exception->getMessage(),
                'code' => 500,
            ];
        }
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $product = new Product();

            $product->fill($request->only(['title', 'price', 'status']));

            $saved = $product->save();

            if (!$saved) {

                return [
                    'error' => 'true',
                    'msg' => 'Server error',
                    'code' => 500,
                ];
            }

            $relations_saved = $this->insertRelations($request->get('categories'), $product);

            if (!$relations_saved) {
                return [
                    'error' => 'true',
                    'msg' => 'Server error',
                    'code' => 500,
                ];
            }
            DB::commit();

            $product->load('categories');

            return [
                'error' => false,
                'msg' => 'Successfully',
                'data' => $product,
                'code' => 200,
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'error' => 'true',
                'msg' => $exception->getMessage(),
                'code' => 500,
            ];
        }


    }


    public function delete($id)
    {
        $product = $this->getProduct($id);
        if (!$product) {
            return [
                'error' => true,
                'code' => 404,
                'msg' => 'Product not found',
            ];
        }
        $action = $product->delete();
        if ($action) {
            return [
                'error' => false,
                'code' => 200,
                'msg' => 'Successfully',
            ];
        }

        return [
            'error' => true,
            'code' => 500,
            'msg' => 'Server error',
        ];
    }

    public function getProductsByRequest(Request $request, $with_trashed): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        if ($with_trashed && $with_trashed != 0) {
            $query = Product::withTrashed();
        } else {
            $query = Product::query();
        }

        $query->select(['id', 'title', 'price', 'status', 'created_at']);
        $query->with(['categories'=>function($query){
//            $query->select('categories.id as category_id');
        }]);

        $query->when($request->get('product_name'), function ($query) use ($request) {
            $query->where('title', 'LIKE', "%{$request->get('product_name')}%");
        })->when($request->get('category_name'), function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('title', 'LIKE', "%{$request->get('category_name')}%");
            });
//            $query->select('categories.id as categor')
        })->when($request->get('price_from'), function ($query) use ($request) {
            $query->where('price', '>=', $request->get('price_from'));
        })->when($request->get('price_to'), function ($query) use ($request) {
            $query->where('price', '>=', $request->get('price_to'));
        });

        return $query->get();
    }


}
