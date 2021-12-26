<?php

declare(strict_types=1);

namespace Auth\Exception\Handler\Validation;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Status;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        /** @var ValidationException $throwable */
        $errors = $throwable->validator->errors()->getMessages();

        return $response
            ->withHeader('Server', 'Auth')
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(Status::BAD_REQUEST)
            ->withBody(new SwooleStream(json_encode(
                [
                    'errors' => ['message' => $errors]
                ],
                JSON_THROW_ON_ERROR
            )));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}