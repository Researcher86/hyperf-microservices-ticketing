<?php

declare(strict_types=1);

namespace Auth\Controller;

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    #[Inject()]
    protected ContainerInterface $container;

    #[Inject()]
    protected RequestInterface $request;

    #[Inject()]
    protected ResponseInterface $response;

//    #[Inject()]
//    protected SessionInterface $session;
}
