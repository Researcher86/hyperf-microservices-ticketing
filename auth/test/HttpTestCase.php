<?php

declare(strict_types=1);

namespace Auth\Tests;

use Hyperf\Testing\Client;
use Hyperf\Utils\Packer\JsonPacker;
use PHPUnit\Framework\TestCase;

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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = make(Client::class);
        $this->jsonPacker = make(JsonPacker::class);
    }

    public function __call($name, $arguments)
    {
        return $this->client->{$name}(...$arguments);
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
