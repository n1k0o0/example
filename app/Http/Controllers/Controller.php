<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function respondCreated(string|object|array $data): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function respondAccepted(mixed $data): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }
        return response()->json($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @param mixed|null $data
     *
     * @return JsonResponse
     */
    public function respondUnprocessableEntity(array $data): JsonResponse
    {
        return response()->json(
            [
                'status' => 'ERROR',
                'message' => "Указанные данные недействительны.",
                'errors' => $data
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @param mixed|null $message
     *
     * @return JsonResponse
     */
    public function respondUnauthorized(string $message = 'Не авторизован'): JsonResponse
    {
        return response()->json(['message' => $message], Response::HTTP_UNAUTHORIZED);
    }

    /**
     *
     * @return JsonResponse
     */
    public function respondEmpty(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     *
     * @param string $message
     * @return JsonResponse
     */
    public function respondNotFound(string $message): JsonResponse
    {
        return response()->json(['message' => $message], Response::HTTP_NOT_FOUND);
    }

    /**
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function respondError(string|object|array $data = ''): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        } else {
            $data = ['errors' => (object)$data];
        }
        return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function respondSuccess(string|object|array $data): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
