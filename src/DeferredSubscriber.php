<?php


namespace Enl\DeferredDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class subscribes itself on all events add by the package:
 * * `Events::DEFER_EVENT`
 * * `Events::PLAY_DEFERRED`
 *
 * @package Enl\DeferredDispatcher
 * @author Alex Panshin <deadyaga@gmail.com>
 */
class DeferredSubscriber implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $eventsToDefer = [];

    /**
     * @var array [string, Event]
     */
    protected $deferredEvents = [];

    /**
     * @param string[] $eventsToDefer list of event names to defer
     */
    public function __construct($eventsToDefer)
    {
        $this->eventsToDefer = $eventsToDefer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::DEFER_EVENT => 'deferEvent',
            Events::PLAY_DEFERRED => 'playDeferred'
        ];
    }

    /**
     * Checks if event should be deferred and defers it
     *
     * @param DeferEvent $event
     */
    public function deferEvent(DeferEvent $event)
    {
        if (in_array($event->getName(), $this->eventsToDefer, true)) {
            $this->deferredEvents[] = [$event->getName(), $event->getRealEvent()];
            $event->defer();
        }
    }

    /**
     * Plays all deferred events
     *
     * @param Event $e
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function playDeferred(Event $e, $eventName, EventDispatcherInterface $dispatcher)
    {
        // to prevent deferring again
        $dispatcher->removeSubscriber($this);

        while (list($name, $event) = array_pop($this->deferredEvents)) {
            $dispatcher->dispatch($name, $event);
        }
    }
}
