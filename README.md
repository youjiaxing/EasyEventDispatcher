

# EventDispatcher 
```php
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
```

## EXAMPLE 1
```php
require "vendor/autoload.php";
use EasyEvent\EventDispatcher

EventDispatcher::listen("test", function ($msg, $event) {
    return "event \"$event\" with msg \"$msg\" handled.";
});

EventDispatcher::listen("test", function ($msg, $event) {
    return "listener2";
});

// return(Array) ['event "test" with msg "hello" handled.']
var_dump(EventDispatcher::dispatch("test", "hello",'array', true));

// return(String) 'event "test" with msg "hello" handled.'
var_dump(EventDispatcher::dispatch("test", "hello",'string', true));

// return(String) ['event "test" with msg "hello" handled.', 'listener2']
var_dump(EventDispatcher::dispatch("test", "hello",'string'));

// return(Array) [['event "test" with msg "hello" handled.', 'listener2']]
var_dump(EventDispatcher::dispatch("test", "hello",'array'));
```

## EXAMPLE 2
```php
require "vendor/autoload.php";
use EasyEvent\EventDispatcher

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

EventDispatcher::listen("event", [new TestObj(), "test"]);
EventDispatcher::listen("event", "TestObj@test");
EventDispatcher::listen("event", ["TestObj", "staticTest"]);

var_dump(EventDispatcher::dispatch("event", [['a', 'b', 'c']]));
```