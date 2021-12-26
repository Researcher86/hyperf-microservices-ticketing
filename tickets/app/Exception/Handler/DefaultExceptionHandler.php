<?php

declare(strict_types=1);

namespace Tickets\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Status;
use Throwable;

class DefaultExceptionHandler extends ExceptionHandler
{
    protected StdoutLoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        return $response
            ->withHeader('Server', 'Auth')
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(Status::INTERNAL_SERVER_ERROR)
            ->withBody(new SwooleStream(json_encode(
                [
                    'errors' => ['message' => Status::getReasonPhrase(Status::INTERNAL_SERVER_ERROR)]
                ],
                JSON_THROW_ON_ERROR
            )));

    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
