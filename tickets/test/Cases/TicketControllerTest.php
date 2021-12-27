<?php

declare(strict_types=1);

namespace Tickets\Tests\Cases;

use Tickets\Model\Ticket;
use Tickets\Tests\HttpTestCase;
use Hyperf\HttpMessage\Server\Response;
use Swoole\Http\Status;

/**
 * @internal
 * @coversNothing
 */
class TicketControllerTest extends HttpTestCase
{
    public function testGetTickets()
    {
        $ticket = Ticket::create([
            'title' => 'Ticket 1',
            'user_id' => 1,
            'price' => 5
        ]);

        /** @var Response $response */
        $response = $this->request('GET', 'api/tickets');

        $this->assertEquals(Status::OK, $response->getStatusCode());

        $data = $this->jsonPacker->unpack((string) $response->getBody());
        $this->assertCount(1, $data);
    }

    public function testGetTicket()
    {
        $ticket = Ticket::create([
            'title' => 'Ticket 1',
            'user_id' => 1,
            'price' => 5
        ]);

        /** @var Response $response */
        $response = $this->request('GET', 'api/tickets/' . $ticket->id);

        $this->assertEquals(Status::OK, $response->getStatusCode());

        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(1, $data['user_id']);
        $this->assertEquals('Ticket 1', $data['title']);
        $this->assertEquals(5, $data['price']);
    }

    public function testGetTicketNotFound()
    {
        /** @var Response $response */
        $response = $this->request('GET', 'api/tickets/-1');

        $this->assertEquals(Status::NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateTicketNotValidTitle()
    {
        $token = $this->generateToken(['id' => 1, 'email' => 'test@test.com']);
        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => null, 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => '', 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => ' ', 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    public function testCreateTicketNotValidPrice()
    {
        $token = $this->generateToken(['id' => 1, 'email' => 'test@test.com']);
        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => 0]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => -1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => null]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateicketNotValidTitle()
    {
        $token = $this->generateToken(['id' => 1, 'email' => 'test@test.com']);
        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => null, 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => '', 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => ' ', 'price' => 1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateTicketNotValidPrice()
    {
        $token = $this->generateToken(['id' => 1, 'email' => 'test@test.com']);
        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => 0]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => -1]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());

        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['headers' => ['Token' => $token], 'form_params' => ['title' => 'Ticket', 'price' => null]]);
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    public function testCreateTicketNotAuthorized()
    {
        $data = ['title' => 'New ticket', 'price' => 10];

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['form_params' => $data]);
        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUpdateTicketNotAuthorized()
    {
        $data = ['title' => 'Ticket 2', 'price' => 20];

        /** @var Response $response */
        $response = $this->request('PUT', 'api/tickets/1/update', ['form_params' => $data]);
        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateTicketSuccess()
    {
        $token = $this->generateToken(['id' => $userId = 5, 'email' => 'test@test.com']);
        $data = ['title' => $title = 'New ticket', 'price' => $price = 10];

        /** @var Response $response */
        $response = $this->request('POST', 'api/tickets/create', ['headers' => ['Token' => $token], 'form_params' => $data]);

        $this->assertEquals(Status::CREATED, $response->getStatusCode());

        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals($userId, $data['user_id']);
        $this->assertEquals($title, $data['title']);
        $this->assertEquals($price, $data['price']);

        $ticket = Ticket::query()->where('user_id', '=', $userId)->where('title', '=', $title)->first();
        $this->assertNotEmpty($ticket);
    }

    public function testUpdateTicketSuccess()
    {
        $ticket = Ticket::create([
            'user_id' => $userId = 5,
            'title' => 'Ticket',
            'price' => 10
        ]);

        $token = $this->generateToken(['id' => 5, 'email' => 'test@test.com']);
        $data = ['title' => $title = 'Ticket 2', 'price' => $price = 20];

        /** @var Response $response */
        $response = $this->request('PUT',
            sprintf('api/tickets/%d/update', $ticket->id),
            ['headers' => ['Token' => $token], 'form_params' => $data]
        );

        $this->assertEquals(Status::OK, $response->getStatusCode());

        $ticket = Ticket::find($ticket->id);
        $this->assertEquals($userId, $ticket->user_id);
        $this->assertEquals($title, $ticket->title);
        $this->assertEquals($price, $ticket->price);
    }

    public function testUpdateTicketUserCannotEditAnotherUsersTicket()
    {
        $ticket = Ticket::create([
            'user_id' => 5,
            'title' => 'Ticket',
            'price' => 10
        ]);

        $token = $this->generateToken(['id' => 555, 'email' => 'test@test.com']);
        $data = ['title' => 'Ticket 2', 'price' => 20];

        /** @var Response $response */
        $response = $this->request('PUT',
            sprintf('api/tickets/%d/update', $ticket->id),
            ['headers' => ['Token' => $token], 'form_params' => $data]
        );

        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }
}
