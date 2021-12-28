<?php

declare(strict_types=1);

namespace Tickets\Tests;

use Hyperf\Testing\Client;
use Hyperf\Utils\Packer\JsonPacker;
use PHPUnit\Framework\TestCase;
use Tickets\Service\JwtService;

/**
 * Class HttpTestCase.
 * @method get($uri, $data = [], $headers = [])
 * @method post($uri, $data = [], $headers = [])
 * @method json($uri, $data = [], $headers = [])
 * @method file($uri, $data = [], $headers = [])
 * @method request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    protected Client $client;
    protected JsonPacker $jsonPacker;
    protected JwtService $jwtService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = make(Client::class);
        $this->jsonPacker = make(JsonPacker::class);
        $this->jwtService = make(JwtService::class);
    }

    public function __call($name, $arguments)
    {
        return $this->client->{$name}(...$arguments);
    }

    public function generateToken(array $data): string
    {
        return $this->jwtService->encode($data);
    }

//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        Db::beginTransaction();
//
//        var_dump('beginTransaction');
//    }
//
//    protected function tearDown(): void
//    {
//        Db::rollBack();
//        var_dump('rollBack');
//
//        parent::tearDown();
//    }
}
