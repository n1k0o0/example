<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BusinessLogicException extends Exception
{
    public function render(): JsonResponse
    {
        $data['message'] = $this->getMessage();
        $code = $this->getCode() === 0 ? 400 : $this->getCode();
        return new JsonResponse($data, $code ?? 400, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
