<?php

declare(strict_types=1);

namespace Auth\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        return $response
            ->withHeader('Server', 'Auth')
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus($throwable->getStatusCode())
            ->withBody(new SwooleStream(json_encode(
                [
                    'errors' => ['message' => $throwable->getMessage()]
                ],
                JSON_THROW_ON_ERROR
            )));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
