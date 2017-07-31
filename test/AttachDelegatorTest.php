<?php

namespace ZfConfigListenerTest;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use stdClass;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use ZfConfigListener\AttachEventDelegator;

class AttachEventDelegatorTest extends PHPUnit_Framework_TestCase
{
    private $delegator;

    protected function setUp()
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

        $eventManagerAware = $this->getMock(EventManagerAwareInterface::class);

        $this->setExpectedException(RuntimeException::class);

        $this->delegator->__invoke($container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });
    }

    public function testThrowExceptionIfServiceNotEventManagerAware()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->delegator->__invoke($this->createContainer([]), 'boo', function() {
            return new stdClass();
        });
    }

    public function testAttachListener()
    {
        $attached = false;
        $listener = $this->getMock(ListenerAggregateInterface::class);
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

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)->getMock();

        $eventManagerAware = $this->getMockBuilder(EventManagerAwareInterface::class)->getMock();
        $eventManagerAware->method('getEventManager')->willReturn($eventManager);

        $this->delegator->__invoke($container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });

        $this->assertTrue($attached);
    }

    private function createContainer(array $services)
    {
        return new ServiceManager(['services' => $services]);
    }
}
