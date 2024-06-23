<?php

namespace App\Services\Global;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    public string|null $status = "success";
    public string|null $message = "";
    public int|null $statusCode = 200;
    public mixed $data = null;

    /*public function status(string $status = "success"): self
    {
        $this->status = $status;
        return $this;
    }*/
    public function success(): self
    {
        $this->status = "success";
        return $this;
    }

    public function failed(): self
    {
        $this->status = "failed";
        return $this;
    }

    public function statusCode(int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function message(string $message = ""): self
    {
        $this->message = $message;
        return $this;
    }

    public function data(mixed $data = null): self
    {
        $this->data = $data;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): object
    {
        return $this->data;
    }

    public function isFailed(): bool
    {
        return $this->status !== "success";
    }

    public function created(): self
    {
        $this->statusCode(Response::HTTP_CREATED);
        return $this;
    }

    public function noContent(): self
    {
        $this->statusCode(Response::HTTP_NO_CONTENT);
        return $this;
    }

    public function deleted(): self
    {
        $this->statusCode(Response::HTTP_NO_CONTENT);
        return $this;
    }

    public function badRequest(): self
    {
        $this->statusCode(Response::HTTP_BAD_REQUEST);
        return $this;
    }

    public function notFound(): self
    {
        $this->statusCode(Response::HTTP_NOT_FOUND);
        return $this;
    }

    public function unAuthorized(): self
    {
        $this->statusCode(Response::HTTP_UNAUTHORIZED);
        return $this;
    }

    public function unProcessableEntity(): self
    {
        $this->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        return $this;
    }

    public function serverError(): self
    {
        $this->statusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        return $this;
    }

    public function getApiResponse(): JsonResponse
    {
        return $this->responseApi(
            status: $this->status,
            message: $this->message,
            data: $this->data,
            code: $this->statusCode
        );
    }

    public function getApiResponseWithCookie($cookie = null): JsonResponse
    {
        return $this->responseApi(
            status: $this->status,
            message: $this->message,
            data: $this->data,
            code: $this->statusCode
        )->cookie($cookie);
    }

    public function getApiResponseCollection(string $collectionName): JsonResponse
    {
        return $this->responseApi(
            status: $this->status,
            message: $this->message,
            data: new $collectionName($this->data),
            code: $this->statusCode
        );
    }

    /**
     * @param $status
     * @param $message
     * @param $data
     * @param  int  $code
     * @param  array  $headers
     * @return JsonResponse
     */
    private function responseApi(string $status,$message = null,$data = null, int $code = 200, array $headers = []): JsonResponse
    {
        return response()->json(
            compact('status', 'code', 'message', 'data'),
            $code,
            $headers,
            JSON_UNESCAPED_UNICODE
        );
    }
}
