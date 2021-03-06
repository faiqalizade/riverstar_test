<?php

namespace App\Repository\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategoryRel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{

    /**
     * @param int $id
     * @return mixed
     */
    public function getProduct(int $id): mixed
    {
        return Product::findOrFail($id);
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

    /**
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function update(Request $request,int $id): array
    {
        try {
            DB::beginTransaction();

            $product = $this->getProduct($id);

            $product->fill($request->only(['title', 'price', 'status']));

            if (!$product->save()) {
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
                'resource' => ProductResource::class,
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

    /**
     * @param Request $request
     * @return array
     */

    public function create(Request $request) : array
    {
        try {
            DB::beginTransaction();

            $product = new Product();

            $product->fill($request->only(['title', 'price', 'status']));

            if (!$product->save()) {
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
                'resource' => ProductResource::class,
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

    /**
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        $product = $this->getProduct($id);

        if ($product->delete()) {
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

    /**
     * @param Request $request
     * @param $with_trashed
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */

    public function getProductsByRequest(Request $request, $with_trashed): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        if ($with_trashed && $with_trashed != 0) {
            $query = Product::withTrashed();
        } else {
            $query = Product::query();
        }

        $query->select(['id', 'title', 'price', 'status', 'created_at']);
        $query->with('categories');

        $query->when($request->get('product_name'), function ($query) use ($request) {
            $query->where('title', 'LIKE', "%{$request->get('product_name')}%");
        })->when($request->get('category_name'), function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('title', 'LIKE', "%{$request->get('category_name')}%");
            });
        })->when($request->get('price_from'), function ($query) use ($request) {
            $query->where('price', '>=', $request->get('price_from'));
        })->when($request->get('price_to'), function ($query) use ($request) {
            $query->where('price', '>=', $request->get('price_to'));
        });

        return $query->get();
    }


}
