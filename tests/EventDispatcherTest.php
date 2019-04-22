<?php

/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/22 10:10
 */
class EventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \EasyEvent\EventDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->dispatcher = new \EasyEvent\EventDispatcher();
    }

    public function testGetListener()
    {
        $listener = function () {

        };
        $this->dispatcher->listen("test", $listener);
        $this->assertEquals([$listener], $this->dispatcher->getListeners("test"));
    }

    public function testListenerInterface()
    {
        $listener = new \Test\CustomListener();
        $this->dispatcher->listen("test", $listener);
        $this->assertEquals([$this->dispatcher->makeListener($listener)], $this->dispatcher->getListeners("test"));

        $resp = $this->dispatcher->dispatch("test", "it's payload");
        $this->assertEquals(["it's payload"], $resp);
    }

    public function testClosureListener()
    {
        $dispatcher = $this->dispatcher;

        $closure1 = function ($msg, $event) {
            return $msg . " " . $event;
        };
        $closure2 = function () {
            return 123;
        };
        $dispatcher->listen(__METHOD__, $closure1);
        $dispatcher->listen(__METHOD__, $closure2);
        $msg = "msg";
        $this->assertEquals([$msg . " " . __METHOD__, 123], $dispatcher->dispatch(__METHOD__, $msg));
        $this->assertEquals($msg . " " . __METHOD__, $dispatcher->dispatch(__METHOD__, $msg, true));
    }

    public function testCallableListener()
    {
        $dispatcher = $this->dispatcher;

        $event = "tEsT";
        $dispatcher->listen($event, "strtoupper");
        $this->assertEquals(strtoupper($event), $dispatcher->dispatch($event, [], true));
        $this->assertEquals([strtoupper($event)], $dispatcher->dispatch($event));
    }

    public function testCallableListener2()
    {
        $dispatcher = $this->dispatcher;
        $event = __METHOD__;

        $listener = new \Test\InvokeListener();
        $dispatcher->listen($event, $listener);

        $this->assertEquals([$listener($event)], $dispatcher->dispatch($event));
    }

    public function testStrListener()
    {
        $dispatcher = $this->dispatcher;
        $event = __METHOD__;

        $dispatcher->listen($event, \Test\CustomListener::class);
        $this->assertEquals(["test"], $dispatcher->dispatch($event, "test"));
    }
}