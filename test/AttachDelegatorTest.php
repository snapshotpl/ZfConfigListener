<?php

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfConfigListener\AttachDelegator;

class AttachDelegatorTest extends PHPUnit_Framework_TestCase
{
    private $delegator;
    private $serviceLocator;

    protected function setUp()
    {
        $this->delegator = new AttachDelegator();
        $this->serviceLocator = $this->getMock(ServiceLocatorInterface::class);
    }

    public function testThrowExceptionIfServiceNotConfigured()
    {
        $this->serviceLocator->method('get')->willReturnMap([
            [
                'Config',
                [
                    'listeners_config' => [],
                ],
            ],
        ]);

        $eventManagerAware = $this->getMock(EventManagerAwareInterface::class);

        $this->setExpectedException(RuntimeException::class);

        $this->delegator->createDelegatorWithName($this->serviceLocator, 'boo', 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });
    }

    public function testThrowExceptionIfServiceNotEventManagerAware()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->delegator->createDelegatorWithName($this->serviceLocator, 'boo', 'boo', function() {
            return new stdClass();
        });
    }

    public function testAttachListener()
    {
        $listener = $this->getMock(ListenerAggregateInterface::class);

        $this->serviceLocator->method('get')->willReturnMap([
            [
                'Config',
                [
                    'listeners_config' => [
                        'boo' => ['foo']
                    ],
                ],
            ],
            [
                'foo',
                $listener,
            ]
        ]);

        $eventManager = $this->getMock(EventManagerInterface::class);

        $eventManagerAware = $this->getMock(EventManagerAwareInterface::class);
        $eventManagerAware->method('getEventManager')->willReturn($eventManager);

        $result = $this->delegator->createDelegatorWithName($this->serviceLocator, 'boo', 'boo', function() use ($eventManagerAware) {
            return $eventManagerAware;
        });

        $this->assertSame($eventManagerAware, $result);
    }
}
