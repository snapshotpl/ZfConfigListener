<?php

namespace ZfConfigListenerTest;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use stdClass;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZfConfigListener\AttachDelegator;

class AttachDelegatorTest extends PHPUnit_Framework_TestCase
{

    private $delegator;
    private $container;

    protected function setUp()
    {
        $this->delegator = new AttachDelegator();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
    }

    public function testThrowExceptionIfServiceNotConfigured()
    {
        $this->container->method('get')->willReturnMap([
            [
                'Config',
                [
                    'listeners_config' => [],
                ],
            ],
        ]);

        $eventManagerAware = $this->getMockBuilder(EventManagerAwareInterface::class)->getMock();

        $this->setExpectedException(RuntimeException::class);

        $this->delegator->__invoke($this->container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });
    }

    public function testThrowExceptionIfServiceNotEventManagerAware()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->delegator->__invoke($this->container, 'boo', function() {
            return new stdClass();
        });
    }

    public function testAttachListener()
    {
        $listener = $this->getMockBuilder(ListenerAggregateInterface::class)->getMock();

        $config = [
            'listeners_config' => [
                'boo' => [
                    'foo',
                ],
            ],
        ];

        $returnMap = [
                ['Config', $config],
                ['foo', $listener],
        ];

        $this->container->method('get')->willReturnMap($returnMap);

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)->getMock();

        $eventManagerAware = $this->getMockBuilder(EventManagerAwareInterface::class)->getMock();
        $eventManagerAware->method('getEventManager')->willReturn($eventManager);

        $result = $this->delegator->__invoke($this->container, 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });

        $this->assertSame($eventManagerAware, $result);
    }
}
