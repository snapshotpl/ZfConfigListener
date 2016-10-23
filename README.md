# ZfConfigListener [![Build Status](https://travis-ci.org/snapshotpl/ZfConfigListener.svg?branch=master)](https://travis-ci.org/snapshotpl/ZfConfigListener)

Attach any Zend Framework Event Manager listener using configuration

## Usage

If you want to attach `my-listener` and `module-listener` to `my-service` service with event manager, you need to configure listener:

```php
<?php

return [
    'service_manager' => [
        'factories' => [
            'my-service' => MyServiceFactory::class,
            'my-listener' => MyListenerFactory::class,
            'module-listener' => ModuleListenerFactory::class,
        ],
    ],
    'listeners_config' => [
        'my-service' => [
            'my-listener',
            'module-listener',
        ],
    ],
];
```

## Installation

```
composer require snapshotpl/zf-config-listener
```

add add `ZfConfigListener` module to application's configuration.
