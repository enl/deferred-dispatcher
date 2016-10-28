<?php


namespace Enl\DeferredDispatcher;

/**
 * This class is just an enum for events
 *
 * @package Enl\DeferredDispatcher
 * @author Alex Panshin <deadyaga@gmail.com>
 */
final class Events
{
    /**
     * This event is fired by Dispatcher itself each time `EventDispatcherInterface::dispatch()` is called.
     * Subscriber of the event MUST expect `DeferEvent` instance
     *
     * @see \Enl\DeferredDispatcher\DeferEvent
     * @see \Enl\DeferredDispatcher\DeferredEventDispatcher
     */
    const DEFER_EVENT = 'defer.new_event';

    /**
     * This event should by fire by Application code when it decides that it's time to play all deferred events
     * Please note, that Subscriber of this event SHOULD remove subscriber of `DEFER_EVENT` to avoid infinite loops
     *
     * @see \Enl\DeferredDispatcher\DeferredSubscriber::playDeferred()
     */
    const PLAY_DEFERRED = 'defer.play_deferred';
}
