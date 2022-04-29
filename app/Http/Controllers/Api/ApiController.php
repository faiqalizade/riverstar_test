<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 *
 */
class ApiController extends Controller
{

    public function getResponse($action): JsonResponse
    {
        if (!$action['error']) {
            $res = [
                'error' => false,
                'message' => 'Successfully',
            ];
            if (isset($action['data']) && $action['data'] && isset($action['resource'])) {
                $res['data'] = new $action['resource']($action['data']);
            }
            return response()->json($res);
        }

        return $this->getErrorResponse($action['code'], $action['msg']);

    }

    public function getErrorResponse($code = 500, $message = 'Server error', $errors = []): JsonResponse
    {
        $result = [
            'error' => true,
            'message' => $message,
        ];
        if ($errors) {
            $result['errors'] = $errors;
        }

        return response()->json($result, $code);
    }

}
