<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;




    public function respond($data = [], $response_message = 'Success', $statusCode = Response::HTTP_OK)
    {

        $message = [
            'status' => true,
            'message' => $response_message,
            'data' => $data,
        ];
        return response()->json($message, $statusCode);
    }
}
