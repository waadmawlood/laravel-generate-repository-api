<?php

namespace Waad\Repository\Traits;

trait Responsable
{
    /**
     * Response json Data
     *
     * @param string $message
     * @param mixed $data
     * @param int|null $status
     * @param bool|null $success
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponce(string|null $message = null, mixed $data = null, int|null $status = 200, bool|null $success = true)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
