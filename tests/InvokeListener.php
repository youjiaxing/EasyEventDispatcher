<?php

namespace Test;

/**
 *
 * @author : 尤嘉兴
 * @version: 2019/4/22 10:52
 */
class InvokeListener
{
    public function __invoke($payload)
    {
        return "__invoke";
    }

}