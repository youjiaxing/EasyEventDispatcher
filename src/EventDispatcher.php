<?php

namespace EasyEvent;

/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/19 9:30
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * 已注册的事件监听器
     * @var array
     */
    protected static $listeners = [];

    protected $handleMethodName = "handle";

    /**
     * 新增监听事件及监听者
     *
     * @param string         $event string 事件名称
     * @param \Closure|mixed $listener
     *
     * @return void
     */
    public static function listen($events, $listener)
    {
        $listener = (new self)->makeListener($listener);
        foreach ((array)$events as $event) {
            self::$listeners[$event][] = $listener;
        }
    }

    /**
     * 构建统一的监听器类型(闭包)
     *
     * @param $listener
     *
     * @return \Closure
     */
    public function makeListener($listener)
    {
        return function ($event, $payload) use ($listener) {
            if ($listener instanceof ListenerInterface) {
                $listener = [$listener, $this->getHandleMethodName()];
            } elseif ($listener instanceof \Closure) {
                // blank
            } elseif (is_callable($listener)) {
                // blank
            } elseif (is_string($listener)) {
                $listener = $this->createClassCallable($listener);
            }

            $payload[] = $event;

            return call_user_func_array($listener, $payload);
        };
    }

    /**
     * 默认事件处理类的方法名
     *
     * @return string
     */
    protected function getHandleMethodName()
    {
        return $this->handleMethodName;
    }

    /**
     * 根据所给的处理事件的类的callable
     *
     * @param string $listener 支持 "类名" 或 "类名@方法名"
     *
     * @return array
     */
    protected function createClassCallable($listener)
    {
        list($listener, $method) = $this->resolveStrListener($listener);

        return [new $listener(), $method];
    }

    /**
     * 解析字符串类型的监听器, 将其转换为 callable
     *
     * @param $listener
     *
     * @return array
     */
    protected function resolveStrListener($listener)
    {
        return strpos($listener, '@') === false ? [$listener, $this->getHandleMethodName()] : explode('@', $listener, 2);
    }


    /**
     * 事件分发
     *  若 $event 是一个对象, 则等价调用 dispatch($event::class, [$event, ...$payload, $halt]
     *
     * @param object|string $event
     * @param array         $payload
     *
     * @return mixed
     */
    public static function dispatch($event, $payload = [], $halt = false)
    {
        list($event, $payload) = (new self)->resolveEventAndPayload($event, $payload);

        $responses = [];
            
            foreach (self::getListeners($event) as $listener) {
                $resp = call_user_func($listener, $event, $payload);
                if ($halt && !is_null($resp)) {
                    return $resp;
                }
                $responses[] = $resp;
            }
        


        return $halt ? null : $responses;
    }

    /**
     * 解析所给的 event 和 payload, 为监听器调用作准备
     *
     * @param string|object $event
     * @param mixed         $payload
     *
     * @return array
     */
    protected function resolveEventAndPayload($event, $payload)
    {
        $payload = is_array($payload) ? $payload : [$payload];
        if (is_object($event)) {
            array_unshift($payload, $event);
            $event = get_class($event);
        }

        return [$event, $payload];
    }


    	/**
	 *
	 * @access	public
	 * @param	string	
	 * @return	bool	
	 */

	/**
	 * Checks if the event has listeners
	 *
	 * @param string $event The name of the event
	 * @return boolean Whether the event has listeners
	 */
	public static function has_listeners($event)
	{

        return (isset(self::$listeners[$event]) and count(self::$listeners[$event]) > 0);
	}

    

    /**
     * 获取所有给定事件对应的listener
     *
     * @param $event
     *
     * @return mixed
     */
    public static function getListeners($event)
    {

        return (!empty($event) && self::has_listeners($event)) ? self::$listeners[$event] : [];

    }

    public static function getAllListeners()
    {

        return self::$listeners;

    }

}