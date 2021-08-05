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
        $events_unique=[];
        $listener = self::makeListener($listener);
        foreach ((array)$events as $event) {

            if(!in_array($event,$events_unique)){
                self::$listeners[$event][] = $listener;
                $events_unique[]=$event;
            }

        }
    }

    /**
     * 构建统一的监听器类型(闭包)
     *
     * @param $listener
     *
     * @return \Closure
     */
    protected static function makeListener($listener)
    {
        return function ($event, $payload) use ($listener) {
            if ($listener instanceof ListenerInterface) {
                $listener = [$listener, self::getHandleMethodName()];
            } elseif ($listener instanceof \Closure) {
                // blank
            } elseif (is_callable($listener)) {
                // blank
            } elseif (is_string($listener)) {
                $listener = self::createClassCallable($listener);
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
    protected static function getHandleMethodName()
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
    protected static function createClassCallable($listener)
    {
        list($listener, $method) = self::resolveStrListener($listener);
        if(class_exists($listener)){
            return [new $listener(), $method];
        }
        throw new \Exception("A class named {$listener} is not loaded");
    }

    /**
     * 解析字符串类型的监听器, 将其转换为 callable
     *
     * @param $listener
     *
     * @return array
     */
    protected static function resolveStrListener($listener)
    {
        if(strpos($listener, '@') === false){
            throw new \Exception("This class must implements the EventDispatcherInterface interface or send a method name parameter to the EventDispatcher::listen static method according to the documentation");
        }else{
            return explode('@', $listener, 2);
        }
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
        list($event, $payload) = self::resolveEventAndPayload($event, $payload);

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
    protected static function resolveEventAndPayload($event, $payload)
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