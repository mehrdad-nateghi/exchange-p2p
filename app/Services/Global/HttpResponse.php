<?php

namespace App\Services\Global;

use Symfony\Component\HttpFoundation\Response;

class HttpResponse
{
    /**
     * @return never
     */
    public function forbidden($message = '')
    {
        abort(code: Response::HTTP_FORBIDDEN, message: $message);
    }

    public function unauthorized($message = '')
    {
        abort(Response::HTTP_UNAUTHORIZED, $message);
    }

    public function notFound($message = '')
    {
        abort(Response::HTTP_NOT_FOUND, $message);
    }

    public function badRequest($message = '')
    {
        abort(Response::HTTP_BAD_REQUEST, $message);
    }

    public function noContent()
    {
        abort(Response::HTTP_NO_CONTENT);
    }

    public function methodNotAllowed()
    {
        abort(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function unprocessableEntity($message = '')
    {
        abort(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    public function tooManyRequests()
    {
        abort(Response::HTTP_TOO_MANY_REQUESTS);
    }

    public function serverError($message = '')
    {
        abort(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
