<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use PhpParser\Node\Expr\Array_;

class RestController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ResponseOk(array $data = [])
    {
        return response()->json(['result' => 'ok', 'data' => $data], 200);
    }


    /**
     * @param string $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function ResponseError(string $message, array $data = [], $code = 200)
    {
        return response()->json(['result' => 'error', 'data' => $data, 'message' => $message], $code);
    }

}
