<?php

namespace App\Traits;

trait ResolvesRejects
{
    use PaginatesOrLists;

    protected function resolve($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function reject($data = null, $message = null, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    public function responseError($data = [], $code = 422)
    {
        return response()->json($data, $code);
    }
}
