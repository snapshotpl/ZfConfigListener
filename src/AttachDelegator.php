<?php

namespace ZfConfigListener;

use RuntimeException;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AttachDelegator implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $eventManagerAware = $callback();

        if (!$eventManagerAware instanceof EventManagerAwareInterface) {
            throw new RuntimeException(sprintf(
                '%s must implements %s',
                get_class($eventManagerAware),
                EventManagerAwareInterface::class
            ));
        }

        $listenersConfig = $serviceLocator->get('Config')['listeners_config'];

        if (isset($listenersConfig[$requestedName]) && is_array($listenersConfig[$requestedName]) && !empty($listenersConfig[$requestedName])) {
            $eventManager = $eventManagerAware->getEventManager();

            foreach ($listenersConfig[$requestedName] as $listenerName) {
                $listener = $serviceLocator->get($listenerName);
                $eventManager->attach($listener);
            }
        } else {
            throw new RuntimeException(sprintf('%s has not configured any listener', $requestedName));
        }
        return $eventManagerAware;
    }
}
