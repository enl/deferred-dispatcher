<?php


namespace Enl\DeferredDispatcher\Test;


use Enl\DeferredDispatcher\Test\Stub\DeferredSubscriber;
use Symfony\Component\EventDispatcher\Event;
use Enl\DeferredDispatcher\DeferredEventDispatcher;

class DeferredEventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    const EVENT_TO_DEFER = 'event-to-defer';

    public function testDispatch()
    {
        $deferer = new DeferredSubscriber([self::EVENT_TO_DEFER]);
        $dispatcher = new DeferredEventDispatcher();

        $dispatcher->addSubscriber($deferer);
        $dispatcher->addListener(self::EVENT_TO_DEFER, function() {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                'This listener should not be executed: event is deferred.'
            );
        });

        $event = $dispatcher->dispatch(self::EVENT_TO_DEFER, new Event());
        $this->assertCount(1, $deferer->getDeferredEvents());
        $this->assertInstanceOf(Event::class, $event, 'Dispatcher should return Event');
    }
}
