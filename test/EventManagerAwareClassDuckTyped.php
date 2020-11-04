<?php

namespace ZfConfigListenerTest;

use Laminas\EventManager\EventManagerInterface;

class EventManagerAwareClassDuckTyped
{
    private $eventManager;

    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }
}
