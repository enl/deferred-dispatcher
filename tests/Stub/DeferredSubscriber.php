<?php


namespace Enl\DeferredDispatcher\Test\Stub;

use Enl\DeferredDispatcher\DeferredSubscriber as BaseSubscriber;

class DeferredSubscriber extends BaseSubscriber
{
    public function getDeferredEvents()
    {
        return $this->deferredEvents;
    }

    public function setDeferredEvents(array $events)
    {
        $this->deferredEvents = $events;
    }
}
