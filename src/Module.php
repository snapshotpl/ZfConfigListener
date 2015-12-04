<?php

namespace ZfConfigListener;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return [
            'listeners_config' => [],
        ];
    }
}
