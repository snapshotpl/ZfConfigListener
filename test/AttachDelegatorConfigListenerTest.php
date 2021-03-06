<?php

namespace ZfConfigListenerTest;

use PHPUnit\Framework\TestCase;
use stdClass;
use Laminas\ModuleManager\Listener\ConfigListener;
use Laminas\ModuleManager\ModuleEvent;
use ZfConfigListener\AttachDelegatorConfigListener;
use ZfConfigListener\AttachEventDelegator;

class AttachDelegatorConfigListenerTest extends TestCase
{
    public function testAttachDelegatorToServiceWithoutDelegators()
    {
        $initialConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
        ];

        $expectedConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
            'service_manager' => [
                'delegators' => [
                    'service' => [
                        AttachEventDelegator::class,
                    ],
                ],
            ],
        ];

        $this->mergeConfig($initialConfig, $expectedConfig);
    }

    public function testAttachDelegatorToServiceWithDelegators()
    {
        $initialConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
            'service_manager' => [
                'delegators' => [
                    'service' => [
                        stdClass::class,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
            'service_manager' => [
                'delegators' => [
                    'service' => [
                        stdClass::class,
                        AttachEventDelegator::class,
                    ],
                ],
            ],
        ];

        $this->mergeConfig($initialConfig, $expectedConfig);
    }

    public function testDontAttachDelegatorToServiceWithAttachEventDelegator()
    {
        $initialConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
            'service_manager' => [
                'delegators' => [
                    'service' => [
                        stdClass::class,
                        AttachEventDelegator::class,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'listeners_config' => [
                'service' => [
                    'listener',
                ],
            ],
            'service_manager' => [
                'delegators' => [
                    'service' => [
                        stdClass::class,
                        AttachEventDelegator::class,
                    ],
                ],
            ],
        ];

        $this->mergeConfig($initialConfig, $expectedConfig);
    }

    private function mergeConfig($initialConfig, $expectedConfig)
    {
        $configListener = new ConfigListener();
        $configListener->setMergedConfig($initialConfig);
        $moduleEvent = new ModuleEvent();
        $moduleEvent->setConfigListener($configListener);
        $listener = new AttachDelegatorConfigListener();

        $listener->onMergeConfig($moduleEvent);

        $this->assertSame($expectedConfig, $configListener->getMergedConfig(false));
    }
}
