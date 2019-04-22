<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/19 9:30
 */
require "vendor/autoload.php";

$dispatcher = new \EasyEvent\EventDispatcher();

$dispatcher->listen("test", function ($msg, $event) {
    return "event \"$event\" with msg \"$msg\" handled.";
});

$dispatcher->listen("test", function ($msg, $event) {
    return "listener2";
});

// 返回字符串 'event "test" with msg "hello" handled.'
var_dump($dispatcher->dispatch("test", "hello", true));

// 返回数组 ['event "test" with msg "hello" handled.', 'listener2']
var_dump($dispatcher->dispatch("test", "hello"));