<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Repository\Product\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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
            return $this->getErrorResponse(422,'Validator error',$validator->errors());
        }

        $data = $this->repository->getProductsByRequest($request, $request->get('not_deleted'));

        return response()->json([
            'error' => false,
            'message' => 'Successfully',
            'data' => new ProductCollection($data),
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = $this->repository->getProduct($id);

        $product->load('categories');

        return response()->json([
            'error' => false,
            'message' => 'Successfully',
            'data' => new ProductResource($product),
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
            return $this->getErrorResponse(422,'Validator error',$validator->errors());
        }

        $action = $this->repository->create($request);

        return $this->getResponse($action);

    }



    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'title' => 'required|min:3',
            'price' => 'required|int',
            'categories' => 'required|array|min:2|max:10|exists:categories,id',
            'categories.*' => 'required|int|distinct'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->getErrorResponse(422,'Validator error',$validator->errors());
        }

        $action = $this->repository->update($request,$id);

        return $this->getResponse($action);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $action = $this->repository->delete($id);

        return $this->getResponse($action);
    }
}
