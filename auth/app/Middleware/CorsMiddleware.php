<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class CorsMiddleware implements MiddlewareInterface
{
    private HttpResponse $response;

    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->response->raw('')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', '*')
                ->withHeader('Access-Control-Allow-Headers', '*');
        }

        return $handler->handle($request)
            ->withHeader('Access-Control-Allow-Origin', '*');
    }
}