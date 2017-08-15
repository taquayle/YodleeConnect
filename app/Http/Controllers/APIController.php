<?php namespace App\Http\Controllers;
class APIController extends Controller {
    public function respondNotFound($message = 'Not Found!')
    {
        return response()->json([
            'error'=> true,
            'message' => $message
        ],404);
    }
    public function respond($data,$errorCode,$headers=[])
    {
        $err = [
            'error'=> true,
            'message' => $data
        ];
        return response()->json($err,$errorCode,$headers);
    }
}
