# ZfConfigListener [![Build Status](https://travis-ci.org/snapshotpl/ZfConfigListener.svg?branch=master)](https://travis-ci.org/snapshotpl/ZfConfigListener)
Attach any ZF2 listener using configuration

## Usage

Example:
You want to attach `my-listener` and `module-listener` to `my-service` service with event manager. You need to configure listener:

```php
<?php

return [
    'listeners_config' => [
        'my-service' => [
            'my-listener',
            'module-listener',
        ],
    ],
];
```

And enable delegator to `my-service`:

```php
return [
    'service_manager' => [
        'decorators' => [
            'my-service' => [
                ZfConfigListener\AttachDelegator::class,
            ],
        ],
    ],
];
```
