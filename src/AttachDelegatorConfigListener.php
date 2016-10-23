<?php

namespace ZfConfigListener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ModuleManager\ModuleEvent;

final class AttachDelegatorConfigListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);
    }

    public function onMergeConfig(ModuleEvent $event)
    {
        $config = $event->getConfigListener()->getMergedConfig(false);

        $servicesWithListenersNames = array_keys($config['listeners_config']);

        foreach ($servicesWithListenersNames as $serviceWithListenersName) {
            if ($this->canAttachIntoConfig($config, $serviceWithListenersName)) {
                $config['service_manager']['delegators'][$serviceWithListenersName][] = AttachEventDelegator::class;
            }
        }

        $event->getConfigListener()->setMergedConfig($config);
    }

    private function canAttachIntoConfig(array $config, $serviceName)
    {
        return
            !isset($config['service_manager']['delegators'][$serviceName])
            || array_search(AttachEventDelegator::class, $config['service_manager']['delegators'][$serviceName], true) === false;
    }
}
