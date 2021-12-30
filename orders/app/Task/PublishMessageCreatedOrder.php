<?php

declare(strict_types=1);

namespace Orders\Task;

use Hyperf\Amqp\Producer;
use Hyperf\Task\Annotation\Task;
use Orders\Amqp\Producer\OrderCreated;

class PublishMessageCreatedOrder
{
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    #[Task(-1, 10)]
    public function handle($order)
    {
        $this->producer->produce(new OrderCreated($order));
    }
}