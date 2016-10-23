<?php

namespace ZfConfigListener;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

final class Module implements ConfigProviderInterface, InitProviderInterface
{
    public function getConfig()
    {
        return [
            'listeners_config' => [],
        ];
    }

    public function init(ModuleManagerInterface $moduleManager)
    {
        $eventManager = $moduleManager->getEventManager();

        (new AttachDelegatorConfigListener())->attach($eventManager);
    }
}
