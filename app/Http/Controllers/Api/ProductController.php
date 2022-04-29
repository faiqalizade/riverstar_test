<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class ProductController extends ApiController
{

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $repository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->repository = $productRepository;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $rules = [
            'product_name' => 'min:3|string',
            'category_name' => 'min:3|string',
            'price_from' => 'int',
            'price_to' => 'int',
            'not_deleted' => 'int|in:0,1',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'msg' => 'Validator error',
                'data' => [],
            ], 422);
        }

        $data = $this->repository->getProductsByRequest($request, $request->get('not_deleted'));

        return response()->json([
            'error' => false,
            'msg' => 'Successfully',
            'data' => $data,
        ]);
    }


    public function show($id)
    {
        $product = $this->repository->getProduct($id);
        if (!$product) {
            return response()->json([
                'error' => false,
                'msg' => 'Product not found',
            ], 404);
        }

        $product->load('categories');


        return response()->json([
            'error' => false,
            'msg' => 'Successfully',
            'data' => $product,
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'title' => 'required|min:3',
            'price' => 'required|int',
            'categories' => 'required|array|min:2|max:10|exists:categories,id',
            'categories.*' => 'required|int|distinct',
            'status' => 'required|int|in:0,1'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'msg' => 'Validator error'
            ], 422);
        }

        $action = $this->repository->create($request);

        return $this->getResponse($action);

    }



    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'title' => 'required|min:3',
            'price' => 'required|int',
            'categories' => 'required|array|min:2|max:10|exists:categories,id',
            'categories.*' => 'required|int|distinct'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'msg' => 'Validator error'
            ], 422);
        }

        $action = $this->repository->update($request,$id);

        return $this->getResponse($action);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $action = $this->repository->delete($id);

        return $this->getResponse($action);
    }
}
