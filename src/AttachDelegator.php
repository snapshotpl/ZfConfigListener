<?php

namespace ZfConfigListener;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AttachDelegator implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        return $this($serviceLocator, $requestedName, $callback);
    }

    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $eventManagerAware = $callback();

        if (!$eventManagerAware instanceof EventManagerAwareInterface) {
            throw new RuntimeException(sprintf(
                '%s must implements %s',
                get_class($eventManagerAware),
                EventManagerAwareInterface::class
            ));
        }

        $listenersConfig = $container->get('Config')['listeners_config'];

        if (isset($listenersConfig[$name]) && is_array($listenersConfig[$name]) && !empty($listenersConfig[$name])) {
            $eventManager = $eventManagerAware->getEventManager();

            foreach ($listenersConfig[$name] as $listenerName) {
                /* @var $listener ListenerAggregateInterface */
                $listener = $container->get($listenerName);
                $listener->attach($eventManager);
            }
            return $eventManagerAware;
        }
        throw new RuntimeException(sprintf('%s has not configured any listener', $name));
    }
}
