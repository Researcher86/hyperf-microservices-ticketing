<?php

declare(strict_types=1);

namespace Tickets\Tests\Model;

use PHPUnit\Framework\TestCase;
use Tickets\Model\Ticket;

class TicketTest extends TestCase
{
    public function testVersion()
    {
        $this->expectException(\RuntimeException::class);

        Ticket::create(['id' => 555, 'title' => 'Correct', 'price' => 5, 'user_id' => 123]);

        $firstInstance = Ticket::find(555);
        $secondInstance = Ticket::find(555);

        $firstInstance->price = 10;
        $secondInstance->price = 15;

        $firstInstance->save();

        $storedTicket = Ticket::find(555);
        $this->assertEquals(10, $storedTicket->price);

        $secondInstance->save();
    }

    public function testVersionTwo()
    {
        $this->expectException(\RuntimeException::class);

        Ticket::create(['id' => 666, 'title' => 'Correct', 'price' => 5, 'user_id' => 123]);

        $firstInstance = Ticket::find(666);
        $secondInstance = Ticket::find(666);

        $firstInstance->update(['price' => 10]);
        $storedTicket = Ticket::find(666);
        $this->assertEquals(10, $storedTicket->price);

        $secondInstance->update(['price' => 15]);
    }
}