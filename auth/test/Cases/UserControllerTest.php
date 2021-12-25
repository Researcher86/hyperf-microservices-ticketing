<?php

declare(strict_types=1);

namespace App\Tests\Cases;

use App\Model\User;
use App\Tests\HttpTestCase;
use Hyperf\HttpMessage\Server\Response;
use Swoole\Http\Status;

/**
 * @internal
 * @coversNothing
 */
class UserControllerTest extends HttpTestCase
{
    public function testCurrentUserAuthorize(): void
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

    public function testCurrentUserNotAuthorized(): void
    {
        /** @var Response $response */
        $response = $this->request('GET', 'api/users/currentuser');
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals('Not authorized', $data['errors']['message']);
    }

    public function testSignup(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signup', ['form_params' => ['email' => $email = 'new@new.com', 'password' => 'new123']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertNotEmpty($token);
        $this->assertEquals($email, $data['email']);

        User::query()->where(['email' => $data['email']])->delete();
    }

    public function testSignupDuplicateEmail(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signup', ['form_params' => ['email' => 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEmpty($token);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
        $this->assertNotEmpty($data['errors']['message']);
    }

    public function testSignupInvalidParameters(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signup', ['form_params' => ['email' => 'new', 'password' => 'new']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEmpty($token);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
        $this->assertNotEmpty($data['errors']['message']['email']);
        $this->assertNotEmpty($data['errors']['message']['password']);
    }

    public function testSignin(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => $email = 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertNotEmpty($token);
        $this->assertEquals($email, $data['email']);
    }

    public function testSigninInvalidParameters(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => 'admin', 'password' => 'a']]);
        $token = $response->getHeader('Token')[0] ?? '';
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEmpty($token);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
        $this->assertNotEmpty($data['errors']['message']['email']);
        $this->assertNotEmpty($data['errors']['message']['password']);
    }

    public function testSignout(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signin', ['form_params' => ['email' => 'admin@admin.com', 'password' => 'admin']]);
        $token = $response->getHeader('Token')[0] ?? '';

        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signout', ['headers' => ['Token' => $token]]);
        $token = $response->getHeader('Token')[0];
        $this->assertEmpty($token);
    }

    public function testSignoutUnauthorized(): void
    {
        /** @var Response $response */
        $response = $this->request('POST', 'api/users/signout');
        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }
}
