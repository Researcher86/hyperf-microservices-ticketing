<?php

declare(strict_types=1);

namespace Orders\Controller;

use DateTime;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Task\Task;
use Hyperf\Task\TaskExecutor;
use Hyperf\Utils\Coroutine;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use Orders\Amqp\Producer\OrderCancelled;
use Orders\Amqp\Producer\OrderCreated;
use Orders\Exception\BusinessException;
use Orders\Middleware\AuthMiddleware;
use Orders\Model\Order;
use Orders\Model\Ticket;
use Orders\Request\OrderCreateRequest;
use Orders\Task\PublishMessageCreatedOrder;
use Swoole\Http\Status;

#[Controller(prefix: "/api/orders")]
class OrderController extends AbstractController
{
    #[RequestMapping(path: "", methods: "get")]
    #[Middleware(AuthMiddleware::class)]
    public function getOrders()
    {
        $userId = $this->request->getAttribute('userId');
        $orders = Order::query()
            ->where('user_id', '=', $userId)
            ->orderBy('id')
            ->get();

        foreach ($orders as $order) {
            $order->ticket;
        }

        return $this->response
            ->json($orders)
            ->withStatus(Status::OK);
    }

    #[RequestMapping(path: "{id:\d+}", methods: "get")]
    #[Middleware(AuthMiddleware::class)]
    public function getOrder(int $id)
    {
        $order = Order::find($id);

        if (!$order) {
            throw new HttpException(Status::NOT_FOUND);
        }

        $userId = $this->request->getAttribute('userId');
        if ($order->user_id !== $userId) {
            throw new HttpException(Status::UNAUTHORIZED);
        }

        $order->ticket;

        return $this->response
            ->json($order)
            ->withStatus(Status::OK);
    }

    #[RequestMapping(path: "create", methods: "post")]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(ValidationMiddleware::class)]
    public function create(OrderCreateRequest $request, TaskExecutor $taskExecutor, PublishMessageCreatedOrder $publishMessageCreatedOrder)
    {
        $data = $request->validated();
        $ticketId = $data['ticket_id'];

        if (!Ticket::query()->where('id', '=', $ticketId)->exists()) {
            return $this->response
                ->json([])
                ->withStatus(Status::NOT_FOUND);
        }

        $existingOrder = Order::query()
            ->where('ticket_id', '=', $ticketId)
            ->whereIn('status', Order::STATUS_ACTIVE)
            ->first();

        if ($existingOrder) {
            throw new BusinessException(Status::BAD_REQUEST, 'Ticket is already reserved');
        }

        $data['user_id'] = $this->request->getAttribute('userId');
        $data['status'] = Order::STATUS_CREATED;
        $data['expires_at'] = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
//        $data['expires_at'] = (new DateTime())->modify('+15 seconds')->format('Y-m-d H:i:s');

        $order = Order::create($data);
        $order->ticket;
        $this->producer->produce(new OrderCreated($order));
//        Выполнение фоновой задачки
//        $publishMessageCreatedOrder->handle($order);
//        $taskExecutor->execute(new Task([PublishMessageCreatedOrder::class, 'handle'], [$order]));

        return $this->response
            ->json($order)
            ->withStatus(Status::CREATED);
    }

    #[RequestMapping(path: "{id:\d+}/delete", methods: "delete")]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(ValidationMiddleware::class)]
    public function delete(int $id)
    {
        $userId = $this->request->getAttribute('userId');
        $order = Order::find($id);

        if (!$order) {
            throw new HttpException(Status::NOT_FOUND);
        }

        if ($order->user_id !== $userId) {
            throw new HttpException(Status::UNAUTHORIZED);
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        $order->ticket;
        $this->producer->produce(new OrderCancelled($order));

        return $this->response
            ->json([])
            ->withStatus(Status::NO_CONTENT);
    }
}
