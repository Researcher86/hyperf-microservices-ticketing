<?php

declare(strict_types=1);

namespace Expiration\Job;

use Expiration\Amqp\Producer\ExpirationComplete;
use Hyperf\Amqp\Producer;
use Hyperf\AsyncQueue\Job;

class OrderCompleteJob extends Job
{
    private int $orderId;

    public function __construct(int $orderId)
    {
        // It's best to use normal data here. Don't pass the objects that carry IO, such as PDO objects.
        $this->orderId = $orderId;
    }

    public function handle()
    {
        var_dump("ExpirationComplete");
        $producer = make(Producer::class);
        $producer->produce(new ExpirationComplete($this->orderId));
    }
}
