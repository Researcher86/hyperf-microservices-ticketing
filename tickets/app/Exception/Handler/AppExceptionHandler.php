<?php

declare(strict_types=1);

namespace Tickets\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Tickets\Exception\BusinessException;

class AppExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        return $response
            ->withHeader('Server', 'Auth')
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus($throwable->getCode())
            ->withBody(new SwooleStream(json_encode(
                [
                    'errors' => ['message' => $throwable->getMessage()]
                ],
                JSON_THROW_ON_ERROR
            )));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}
