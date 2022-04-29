<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\Category\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class CategoryController extends ApiController
{

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $repository;

    /**
     * @param CategoryRepositoryInterface $repository
     */
    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'title' => 'min:3|string',
            'not_deleted' => 'int|in:1,2'
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
        $categories = $this->repository->getCategoriesByRequest($request,$request->get('not_deleted'));

        return response()->json([
            'error' => false,
            'msg' => 'Successfully',
            'data' => $categories,
        ]);

    }

    public function show($id){
        $category = $this->repository->getCategory($id);
        if(!$category){
            return response()->json([
                'error' => false,
                'msg' => 'Category not found',
            ],404);
        }

        return response()->json([
            'error' => false,
            'msg' =>  'Successfully',
            'data' => $category,
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'title' => 'required|string|min:3',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'msg' => 'Validator error',
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
            'title' => 'required|string|min:3',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors(),
                'msg' => 'Validator error',
            ], 422);
        }

        $action = $this->repository->update($request, $id);


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
