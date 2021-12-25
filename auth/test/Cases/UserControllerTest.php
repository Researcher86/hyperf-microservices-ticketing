<?php

declare(strict_types=1);

namespace App\Tests\Cases;

use App\Model\User;
use App\Tests\HttpTestCase;
use Hyperf\HttpMessage\Server\Response;

/**
 * @internal
 * @coversNothing
 */
class UserControllerTest extends HttpTestCase
{
    public function testCurrentUserNotAuthorized()
    {
        $response = $this->get('api/users/currentuser');
        $this->assertEquals('Not authorized', $response['errors']['message']);
    }

    public function testCurrentUserAuthorize()
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => $email = 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0];
        $this->assertNotEmpty($token);

        /** @var Response $response */
        $response = $this->request('GET', 'api/users/currentuser', ['headers' => ['Token' => $token]]);
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals($email, $data['currentUser']['email']);
    }

    public function testSignup()
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signup', ['form_params' => ['email' => $email = 'new@new.com', 'password' => 'new123']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertNotEmpty($token);
        $this->assertEquals($email, $data['email']);

        User::query()->where(['email' => $data['email']])->delete();
    }

    public function testSignin()
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => $email = 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertNotEmpty($token);
        $this->assertEquals($email, $data['email']);
    }

    public function testSignout()
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0] ?? '';

        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signout', ['headers' => ['Token' => $token]]);
        $token = $response->getHeader('Token')[0];
        $this->assertEmpty($token);
    }
}
