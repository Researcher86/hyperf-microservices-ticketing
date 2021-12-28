<?php

declare(strict_types=1);

namespace Payments\Controller;

use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use Payments\Amqp\Producer\PaymentCreated;
use Payments\Middleware\AuthMiddleware;
use Payments\Model\Order;
use Payments\Model\Payment;
use Payments\Request\PaymentCreateRequest;
use Swoole\Http\Status;

#[Controller(prefix: "/api/payments")]
class PaymentController extends AbstractController
{
    #[RequestMapping(path: "", methods: "post")]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(ValidationMiddleware::class)]
    public function create(PaymentCreateRequest $request)
    {
        ['token' => $token, 'order_id' => $orderId] = $request->validated();
        $order = Order::find($orderId);
        if (!$order) {
            throw new HttpException(Status::NOT_FOUND);
        }

        $userId = $this->request->getAttribute('userId');

        if ($userId !== $order->user_id) {
            throw new HttpException(Status::UNAUTHORIZED);
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            throw new HttpException(Status::BAD_REQUEST, 'Cannot pay for an cancelled order');
        }

        $payment = Payment::create(['order_id' => $orderId, 'payment_id' => uniqid('payment:', false)]);

        $this->producer->produce(new PaymentCreated($payment));

        return $this->response
            ->json(['id' => $payment->id])
            ->withStatus(Status::CREATED);
    }
}
