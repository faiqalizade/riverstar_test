<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class ApiController extends Controller
{

    public function getResponse($action): \Illuminate\Http\JsonResponse
    {
        if (!$action['error']) {
            $res = [
                'error' => false,
                'msg' => 'Successfully',
            ];
            if(isset($action['data']) && $action['data'] && isset($action['resource'])){
                $res['data'] = new $action['resource']($action['data']);
            }
            return response()->json($res);
        }

        return response()->json([
            'error' => true,
            'msg' => $action['msg'],
        ], $action['code']);
    }

}
