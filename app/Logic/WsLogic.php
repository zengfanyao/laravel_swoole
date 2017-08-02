<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-7-30
 * Time: 下午11:21
 */

namespace App\Logic;


use Illuminate\Support\Facades\Redis;

class WsLogic
{
    public static function onOpen($serv,$request)
    {
        \Log::info($serv->connection_list());
    }
}