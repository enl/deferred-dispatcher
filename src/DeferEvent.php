<?php


namespace Enl\DeferredDispatcher;

use Symfony\Component\EventDispatcher\Event;

/**
 * DeferEvent encapsulates really fired event and its name
 *
 * @package Enl\DeferredDispatcher
 * @author Alex Panshin <deadyaga@gmail.com>
 */
class DeferEvent extends Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Event
     */
    private $realEvent;

    /**
     * @var boolean
     */
    private $deferred = false;

    /**
     * DeferEvent constructor.
     * @param string $name
     * @param Event $event
     */
    public function __construct($name, Event $event = null)
    {
        $this->name = $name;
        $this->realEvent = $event;
    }

    /**
     * Call this function if real event is deferred and will be executed earlier
     * Stops propagation!
     *
     * @return array [string, Event]
     */
    public function defer()
    {
        $this->deferred = true;
        $this->stopPropagation();

        return [$this->name, $this->realEvent];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Event
     */
    public function getRealEvent()
    {
        return $this->realEvent;
    }

    /**
     * @return bool
     */
    public function isDeferred()
    {
        return $this->deferred;
    }
}
