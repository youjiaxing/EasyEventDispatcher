<?php

namespace EasyEvent;

/**
 * 监听器接口
 * @author : 尤嘉兴
 * @version: 2019/4/19 18:19
 */
interface ListenerInterface
{
    /**
     * 回调函数
     *
     * @param $payload
     *
     * @return mixed
     */
    public function handle($payload);
}