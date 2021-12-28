<?php

declare(strict_types=1);

namespace Payments\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Producer;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use Payments\Model\Order;

#[Consumer(exchange: 'orders', routingKey: 'order-created', queue: 'payments-order-created', name: "OrderCreated", nums: 1)]
class OrderCreated extends ConsumerMessage
{
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        Order::create([
            'id' => $data['id'],
            'user_id' => $data['user_id'],
            'price' => $data['ticket']['price'],
            'status' => $data['status'],
            'version' => $data['version']
        ]);

        return Result::ACK;
    }
}
