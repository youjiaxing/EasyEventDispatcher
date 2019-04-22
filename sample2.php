<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/22 13:56
 */
require "vendor/autoload.php";

class TestObj
{
    public function test($arr)
    {
        return implode(', ', $arr);
    }

    public static function staticTest($arr)
    {
        return implode(', ', $arr);
    }
}

$dispatcher = new \EasyEvent\EventDispatcher();
$dispatcher->listen("event", [new TestObj(), "test"]);
$dispatcher->listen("event", "TestObj@test");
$dispatcher->listen("event", ["TestObj", "test"]);

var_dump($dispatcher->dispatch("event", [['a', 'b', 'c']]));