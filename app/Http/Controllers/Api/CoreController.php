<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;

class CoreController extends Controller
{
    private $data = [];
    private $errors = [];

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }


    public function sendResponse($message = '', $type = 'success', $code = 200)
    {
        $response = [
            'type' => $type,
            'code' => $code
        ];

        if (!empty($message))
            $response['message'] = $message;

        if (!!$this->data) {
            $response['data'] = $this->data;
        }

        return response()->json($response, $code);
    }


    public function sendError($message = 'Something went wrong! Please try again.', $code = 400)
    {
        $response = [
            'message' => $message,
            'type' => 'error',
            'code' => $code
        ];

        if (!!$this->errors) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $code);
    }
}
