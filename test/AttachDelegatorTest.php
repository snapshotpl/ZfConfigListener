<?php

namespace ZfConfigListenerTest;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\ServiceManager\ServiceManager;
use ZfConfigListener\AttachEventDelegator;

class AttachEventDelegatorTest extends TestCase
{
    private $delegator;

    protected function setUp(): void
    {
        $this->delegator = new AttachEventDelegator();
    }

    public function testThrowExceptionIfServiceNotConfigured()
    {
        $container = $this->createContainer([
            'Config' => [
                'listeners_config' => [],
            ],
        ]);

        $eventManagerAware = $this->createMock(EventManagerAwareInterface::class);

        $this->expectException(RuntimeException::class);

        $this->delegator->__invoke($container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });
    }

    public function testThrowExceptionIfServiceNotEventManagerAware()
    {
        $this->expectException(RuntimeException::class);

        $this->delegator->__invoke($this->createContainer([]), 'boo', function() {
            return new stdClass();
        });
    }

    public function testAttachListener()
    {
        $attached = false;
        $listener = $this->createMock(ListenerAggregateInterface::class);
        $listener->method('attach')->willReturnCallback(function() use (&$attached) {
            $attached = true;
        });

        $container = $this->createContainer([
            'Config' => [
                'listeners_config' => [
                    'boo' => [
                        'foo',
                    ],
                ],
            ],
            'foo' => $listener,
        ]);

        $eventManager = $this->createMock(EventManagerInterface::class);

        $eventManagerAware = $this->createMock(EventManagerAwareInterface::class);
        $eventManagerAware->method('getEventManager')->willReturn($eventManager);

        $this->delegator->__invoke($container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });

        $this->assertTrue($attached);
    }

    public function testAttachListenerUsingDuckType()
    {
        $attached = false;
        $listener = $this->createMock(ListenerAggregateInterface::class);
        $listener->method('attach')->willReturnCallback(function() use (&$attached) {
            $attached = true;
        });

        $container = $this->createContainer([
            'Config' => [
                'listeners_config' => [
                    'boo' => [
                        'foo',
                    ],
                ],
            ],
            'foo' => $listener,
        ]);

        $eventManager = $this->createMock(EventManagerInterface::class);

        $eventManagerAwareDuckTyped = new EventManagerAwareClassDuckTyped($eventManager);

        $this->delegator->__invoke($container, 'boo', function() use ($eventManagerAwareDuckTyped) {
            return $eventManagerAwareDuckTyped;
        });

        $this->assertTrue($attached);
    }

    private function createContainer(array $services)
    {
        return new ServiceManager(['services' => $services]);
    }
}
