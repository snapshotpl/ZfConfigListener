<?php

namespace ZfConfigListenerTest;

use Zend\EventManager\EventManagerInterface;

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
