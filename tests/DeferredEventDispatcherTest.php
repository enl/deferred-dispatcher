<?php


namespace Enl\DeferredDispatcher\Test;


use Enl\DeferredDispatcher\Events;
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

    public function testPlayDeferred()
    {
        $subscriber = new DeferredSubscriber([self::EVENT_TO_DEFER]);
        $subscriber->setDeferredEvents([
            [self::EVENT_TO_DEFER, new Event()]
        ]);

        $dispatcher = new DeferredEventDispatcher();
        $executed = false;
        $dispatcher->addListener(self::EVENT_TO_DEFER, function() use(&$executed) {
            $executed = true;
        });
        $dispatcher->addSubscriber($subscriber);

        $dispatcher->dispatch(Events::PLAY_DEFERRED);
        $this->assertTrue($executed, 'Listener finally executed');
        $this->assertCount(0, $subscriber->getDeferredEvents(), 'List of deferred events is empty.');
    }

    public function testEventPlaysOnce()
    {
        $subscriber1 = new DeferredSubscriber(['test-event']);
        $subscriber2 = new DeferredSubscriber(['test-event']);

        $dispatcher = new DeferredEventDispatcher();
        $dispatcher->addSubscriber($subscriber1);
        $dispatcher->addSubscriber($subscriber2);

        $times = 0;

        $dispatcher->addListener('test-event', function() use (&$times) {
            $times++;
        });

        $dispatcher->dispatch('test-event');
        $dispatcher->dispatch(Events::PLAY_DEFERRED);

        $this->assertEquals(1, $times, 'Event should be handled exactly 1 time.');
    }
}
