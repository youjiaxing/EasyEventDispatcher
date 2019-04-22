<?php

namespace Test;

/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/22 10:52
 */
class CustomListener implements \EasyEvent\ListenerInterface
{

    /**
     * 回调函数
     *
     * @param $payload
     *
     * @return mixed
     */
    public function handle($payload)
    {
        return $payload;
    }

    public function __invoke($payload)
    {
        return "__invoke";
    }


}