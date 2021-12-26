<?php

declare(strict_types=1);

namespace Auth\Middleware;

use Auth\Model\User;
use Auth\Service\JwtService;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Http\Status;

class AuthMiddleware implements MiddlewareInterface
{
    private HttpResponse $response;
    private JwtService $jwtService;
    private TranslatorInterface $translator;

    public function __construct(HttpResponse $response, JwtService $jwtService, TranslatorInterface $translator)
    {
        $this->response = $response;
        $this->jwtService = $jwtService;
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeader('Token')[0] ?? '';

        $isValidToken = $this->jwtService->verify($token);
        if ($isValidToken) {
            $data = $this->jwtService->decode($token);
            $request = $request->withAttribute('currentUser', $data);

            return $handler->handle($request);
        }

        return $this->response
            ->json([
                'errors' => [
                    'message' => $this->translator->trans('app.token.unauthorized')
                ]
            ])
            ->withStatus(Status::UNAUTHORIZED);
    }
}