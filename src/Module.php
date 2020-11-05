<?php

namespace ZfConfigListener;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;

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
