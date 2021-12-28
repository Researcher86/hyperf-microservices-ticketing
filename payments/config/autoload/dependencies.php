<?php

declare(strict_types=1);

use Payments\Service\JwtService;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

return [
    JwtService::class => function (ContainerInterface $container) {
        return new JwtService(
            env('JWT_SECRET', 'hyperf'),
            (int) env('JWT_TTL', 3600),
            $container->get(LoggerFactory::class)->get('log', 'default')
        );
    }
];
