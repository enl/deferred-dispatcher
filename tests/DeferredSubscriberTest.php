<?php


namespace Enl\DeferredDispatcher\Test;

use Enl\DeferredDispatcher\DeferEvent;
use Enl\DeferredDispatcher\DeferredSubscriber;
use Enl\DeferredDispatcher\Events;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DeferredSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function deferEventProvider()
    {
        return [
            'should be deferred' => [
                ['event-to-defer'], new DeferEvent('event-to-defer', new Event()), true,
            ],
            'empty array of events' => [
                [], new DeferEvent('event-to-defer', new Event()), false,
            ],
            'should not defer event' => [
                ['event-to-defer'], new DeferEvent('event-not-to-defer', new Event()), false
            ]
        ];
    }

    /**
     * @param $deferredEvents
     * @param DeferEvent $event
     * @param $expected
     * @dataProvider deferEventProvider
     */
    public function testDeferEvent($deferredEvents, DeferEvent $event, $expected)
    {
        $subscriber = new DeferredSubscriber($deferredEvents);
        $subscriber->deferEvent($event);

        $this->assertEquals($expected, $event->isDeferred());
    }

    public function testPlayDeferred()
    {
        $event = new Event();
        $subscriber = new Stub\DeferredSubscriber(['event-to-defer']);
        $subscriber->setDeferredEvents([['event-to-defer', $event]]);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);

        $subscriber->playDeferred(new Event(), Events::PLAY_DEFERRED, $dispatcher);

        $this->assertFalse($dispatcher->hasListeners(Events::DEFER_EVENT), 'Subscriber should unsubscribe event catcher.');
        $this->assertEquals([], $subscriber->getDeferredEvents(), 'Deferred events list should be empty AFTER function execution.');
    }

    /**
     * If the test fails it means you'll got a problem when you try to replay empty deferred events list
     */
    public function testNothingToPlayDeferred()
    {
        $subscriber = new Stub\DeferredSubscriber(['event-to-defer']);
        $subscriber->playDeferred(new Event(), Events::PLAY_DEFERRED, new EventDispatcher());

        $this->assertEquals([], $subscriber->getDeferredEvents());
    }

    public function testSubscribedEvents()
    {
        foreach ([Events::DEFER_EVENT, Events::PLAY_DEFERRED] as $expected) {
            $this->assertContains(
                $expected,
                array_keys(DeferredSubscriber::getSubscribedEvents()),
                sprintf('Should be subscribed on %s event.', $expected)
            );
        }
    }

    public function testAddEvent()
    {
        $subscriber = new Stub\DeferredSubscriber();
        $subscriber->addEvent('event-to-defer');
        $this->assertEquals(['event-to-defer'], $subscriber->getEventsToDefer());
    }
}
