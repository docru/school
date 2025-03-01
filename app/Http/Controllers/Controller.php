<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function ResponseOk(array $data = []): JsonResponse
    {
        return response()->json(['result' => 'ok', 'data' => $data]);
    }


    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    public function ResponseError(string $message, array $data = [], int $code = 200): JsonResponse
    {
        return response()->json(['result' => 'error', 'data' => $data, 'message' => $message], $code);
    }

}
