<?php


namespace Enl\DeferredDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Replaces original Symfony EventDispatcher
 *
 * @package Enl\DeferredDispatcher
 * @author Alex Panshin <deadyaga@gmail.com>
 */
class DeferredEventDispatcher extends EventDispatcher
{
    /**
     * @param string $eventName
     * @param Event|null $event
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        // Let's start from trying to defer event
        $deferEvent = new DeferEvent($eventName, $event);
        parent::dispatch(Events::DEFER_EVENT, $deferEvent);

        if (!$deferEvent->isDeferred()) {
            return parent::dispatch($eventName, $event);
        } else {
            return $event;
        }
    }
}
