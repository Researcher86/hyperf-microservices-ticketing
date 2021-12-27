<?php

declare(strict_types=1);

namespace Tickets\Controller;

use Tickets\Amqp\Producer\TicketCreated;
use Tickets\Amqp\Producer\TicketUpdated;
use Tickets\Middleware\AuthMiddleware;
use Tickets\Model\Ticket;
use Tickets\Request\TicketCreateRequest;
use Tickets\Request\TicketUpdateRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use Swoole\Http\Status;

#[Controller(prefix: "/api/tickets")]
class TicketController extends AbstractController
{
    #[RequestMapping(path:"", methods: "get")]
    public function getTickets()
    {
        $tickets = Ticket::all();

        return $this->response
            ->json($tickets)
            ->withStatus(Status::OK);
    }

    #[RequestMapping(path:"{id:\d+}", methods: "get")]
    public function getTicket(int $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return $this->response
                ->json([])
                ->withStatus(Status::NOT_FOUND);
        }

        return $this->response
            ->json($ticket)
            ->withStatus(Status::OK);
    }

    #[RequestMapping(path:"create", methods: "post")]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(ValidationMiddleware::class)]
    public function create(TicketCreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $this->request->getAttribute('userId');

        $ticket = Ticket::create($data);

        $this->producer->produce(new TicketCreated($ticket));

        return $this->response
            ->json($ticket)
            ->withStatus(Status::CREATED);
    }

    #[RequestMapping(path:"{id:\d+}/update", methods: "put")]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(ValidationMiddleware::class)]
    public function update(int $id, TicketUpdateRequest $request)
    {
        $userId = $this->request->getAttribute('userId');
        $ticket = Ticket::find($id);

        if ($ticket->user_id !== $userId)
        {
            return $this->response
                ->json([])
                ->withStatus(Status::UNAUTHORIZED);
        }

        $ticket->update($request->validated());

        $this->producer->produce(new TicketUpdated($ticket));

        return $this->response
            ->json($ticket)
            ->withStatus(Status::OK);
    }
}
