<?php

declare(strict_types=1);

namespace Orders\Tests\Cases;

use DateTime;
use Orders\Model\Order;
use Orders\Model\Ticket;
use Orders\Tests\HttpTestCase;
use Hyperf\HttpMessage\Server\Response;
use Swoole\Http\Status;

/**
 * @internal
 * @coversNothing
 */
class OrderControllerTest extends HttpTestCase
{
    public function testGetUserOrders()
    {
        $userIdOne = 555;
        $userIdTwo = 666;
        $ticketOne = Ticket::create(['title' => 'Ticket 1', 'price' => 10]);
        $ticketTwo = Ticket::create(['title' => 'Ticket 2', 'price' => 20]);
        $ticketThree = Ticket::create(['title' => 'Ticket 3', 'price' => 30]);

        $orderOne = Order::create([
            'user_id' => $userIdOne,
            'ticket_id' => $ticketOne->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);
        $orderTwo = Order::create([
            'user_id' => $userIdOne,
            'ticket_id' => $ticketTwo->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);
        $orderThree = Order::create([
            'user_id' => $userIdTwo,
            'ticket_id' => $ticketThree->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $tokenForUserOne = $this->generateToken(['id' => $userIdOne, 'email' => 'test@test.com']);
        $tokenForUserTwo = $this->generateToken(['id' => $userIdTwo, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('GET', 'api/orders', ['headers' => ['Token' => $tokenForUserOne]]);
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(Status::OK, $response->getStatusCode());
        $this->assertCount(2, $data);
        $this->assertEquals($orderOne->id, $data[0]['id']);
        $this->assertEquals($ticketTwo->id, $data[1]['id']);
        $this->assertEquals($ticketOne->id, $data[0]['ticket']['id']);
        $this->assertEquals($ticketTwo->id, $data[1]['ticket']['id']);

        /** @var Response $response */
        $response = $this->request('GET', 'api/orders', ['headers' => ['Token' => $tokenForUserTwo]]);
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(Status::OK, $response->getStatusCode());
        $this->assertCount(1, $data);
        $this->assertEquals($ticketThree->id, $data[0]['id']);
    }

    public function testGetUserOrder()
    {
        $userIdOne = 555;
        $ticketOne = Ticket::create(['title' => 'Ticket 1', 'price' => 10]);

        $orderOne = Order::create([
            'user_id' => $userIdOne,
            'ticket_id' => $ticketOne->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $tokenForUserOne = $this->generateToken(['id' => $userIdOne, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('GET', 'api/orders/' . $orderOne->id, ['headers' => ['Token' => $tokenForUserOne]]);
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(Status::OK, $response->getStatusCode());
        $this->assertEquals($orderOne->id, $data['id']);
        $this->assertEquals($ticketOne->id, $data['ticket']['id']);
    }

    public function testGetUserOrderNotFound()
    {
        $tokenForUserOne = $this->generateToken(['id' => 666, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('GET', 'api/orders/555', ['headers' => ['Token' => $tokenForUserOne]]);

        $this->assertEquals(Status::NOT_FOUND, $response->getStatusCode());
    }

    public function testGetUserOrderUnauthorized()
    {
        $ticketOne = Ticket::create(['title' => 'Ticket 1', 'price' => 10]);

        $orderOne = Order::create([
            'user_id' => 555,
            'ticket_id' => $ticketOne->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $tokenForUserOne = $this->generateToken(['id' => 666, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('GET', 'api/orders/' . $orderOne->id, ['headers' => ['Token' => $tokenForUserOne]]);

        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteUserOrderNotFound()
    {
        $tokenForUserOne = $this->generateToken(['id' => 555, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('DELETE', 'api/orders/555/delete', ['headers' => ['Token' => $tokenForUserOne]]);

        $this->assertEquals(Status::NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteUserOrderUnauthorized()
    {
        $ticketOne = Ticket::create(['title' => 'Ticket 1', 'price' => 10]);

        $orderOne = Order::create([
            'user_id' => 555,
            'ticket_id' => $ticketOne->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $tokenForUserOne = $this->generateToken(['id' => 666, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('DELETE', sprintf('api/orders/%d/delete', $orderOne->id), ['headers' => ['Token' => $tokenForUserOne]]);

        $this->assertEquals(Status::UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteUserOrderSuccess()
    {
        $userId = 555;
        $ticketOne = Ticket::create(['title' => 'Ticket 1', 'price' => 10]);

        $orderOne = Order::create([
            'user_id' => $userId,
            'ticket_id' => $ticketOne->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $tokenForUserOne = $this->generateToken(['id' => $userId, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('DELETE', sprintf('api/orders/%d/delete', $orderOne->id), ['headers' => ['Token' => $tokenForUserOne]]);
        $data = $this->jsonPacker->unpack((string) $response->getBody());

        $this->assertEquals(Status::NO_CONTENT, $response->getStatusCode());
        $this->assertEquals(Order::STATUS_CANCELLED, $orderOne->fresh()->status);
    }

    public function testTicketDoesNotExists()
    {
        $token = $this->generateToken(['id' => 555, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('POST', 'api/orders/create', ['headers' => ['Token' => $token], 'form_params' => ['ticket_id' => -1]]);

        $this->assertEquals(Status::NOT_FOUND, $response->getStatusCode());
    }

    public function testTicketIsAlreadyReserved()
    {
        $userId = 1;
        $ticket = Ticket::create(['title' => 'Ticket Reserved', 'price' => 10]);
        Order::create([
            'user_id' => $userId,
            'ticket_id' => $ticket->id,
            'status' => Order::STATUS_CREATED,
            'expires_at' => (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s')
        ]);

        $token = $this->generateToken(['id' => $userId, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('POST', 'api/orders/create', ['headers' => ['Token' => $token], 'form_params' => ['ticket_id' => $ticket->id]]);

        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    public function testTicketReserves()
    {
        $ticket = Ticket::create(['title' => 'Ticket Reserved', 'price' => 10]);

        $token = $this->generateToken(['id' => 1, 'email' => 'test@test.com']);

        /** @var Response $response */
        $response = $this->request('POST', 'api/orders/create', ['headers' => ['Token' => $token], 'form_params' => ['ticket_id' => $ticket->id]]);

        $this->assertEquals(Status::CREATED, $response->getStatusCode());
    }
}
