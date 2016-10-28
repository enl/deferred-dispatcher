<?php


namespace Enl\DeferredDispatcher\Test;


use Enl\DeferredDispatcher\DeferEvent;
use Symfony\Component\EventDispatcher\Event;

class DeferEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $wrappedEvent = new Event();
        $event = new DeferEvent('test-name', $wrappedEvent);

        $this->assertEquals('test-name', $event->getName());
        $this->assertEquals($wrappedEvent, $event->getRealEvent());
    }

    public function testDefer()
    {
        $event = new DeferEvent('test-name', new Event());
        $this->assertFalse($event->isDeferred());

        $event->defer();
        $this->assertTrue($event->isDeferred());
    }
}
