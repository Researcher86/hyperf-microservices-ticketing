<?php

declare(strict_types=1);

namespace Orders\Service;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;

class JwtService
{
    private string $jwtKey;
    private int $jwtTtl;
    private LoggerInterface $logger;

    public function __construct(string $jwtKey, int $jwtTtl, LoggerInterface $logger)
    {
        $this->jwtKey = $jwtKey;
        $this->jwtTtl = $jwtTtl;
        $this->logger = $logger;
    }

    public function encode(array $data): string
    {
        $data = array_merge($data, ["exp" => time() + $this->jwtTtl]);

        JWT::$leeway = $this->jwtTtl;
        return JWT::encode($data, $this->jwtKey, 'HS256');
    }

    public function decode(string $jwtToken): array
    {
        return (array)JWT::decode($jwtToken, new Key($this->jwtKey, 'HS256'));
    }

    public function verify(string $jwtToken): bool
    {
        try {
            $this->decode($jwtToken);
            return true;
        } catch (Exception $exception) {
            $this->logger->debug($exception->getMessage(), $exception->getTrace());
            return false;
        }
    }
}