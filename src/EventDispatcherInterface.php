<?php

namespace EasyEvent;

/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/19 9:30
 */
interface EventDispatcherInterface
{
    /**
     * @param string         $event string 事件名称
     * @param \Closure|mixed $listener
     *
     * @return mixed
     */
    public static function listen($event, $listener);

    /**
     * @param EventInterface|string $event
     * @param array                 $payload
     * @param string                $return_type
     * @param bool                  $halt
     *
     * @return mixed
     */
    public static function dispatch($event, $payload = [], $return_type='array', $halt = false);
}