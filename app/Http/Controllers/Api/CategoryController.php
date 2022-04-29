<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Repository\Category\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $rules = [
            'title' => 'min:3|string',
            'not_deleted' => 'int|in:1,2'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->getErrorResponse(422,'Validator error',$validator->errors());
        }
        $categories = $this->repository->getCategoriesByRequest($request,$request->get('not_deleted'));

        return response()->json([
            'error' => false,
            'message' => 'Successfully',
            'data' => new CategoryCollection($categories),
        ]);

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id) : JsonResponse{
        $category = $this->repository->getCategory($id);

        return response()->json([
            'error' => false,
            'message' =>  'Successfully',
            'data' => new CategoryResource($category),
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'title' => 'required|string|min:3',
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
            'title' => 'required|string|min:3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->getErrorResponse(422,'Validator error',$validator->errors());
        }

        $action = $this->repository->update($request, $id);

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
