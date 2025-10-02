<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * 統一的 JSON 回應格式
     *
     * @param bool $success 是否成功
     * @param string $message 回應訊息
     * @param mixed $data 回應數據
     * @param int $status HTTP 狀態碼
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($success, $message, $data = null, $status = 200)
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * 成功回應
     *
     * @param string $message 成功訊息
     * @param mixed $data 回應數據
     * @param int $status HTTP 狀態碼
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($message, $data = null, $status = 200)
    {
        return $this->jsonResponse(true, $message, $data, $status);
    }

    /**
     * 錯誤回應
     *
     * @param string $message 錯誤訊息
     * @param mixed $data 錯誤數據
     * @param int $status HTTP 狀態碼
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $data = null, $status = 400)
    {
        return $this->jsonResponse(false, $message, $data, $status);
    }
}
