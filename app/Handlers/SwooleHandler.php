<?php
namespace App\Handlers;
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-7-30
 * Time: 下午1:39
 */
class SwooleHandler
{

    public function __construct()
    {

    }
    public function onStart($serv)
    {
        $array=[
            'master'=>$serv->master_pid,
            'manager'=>$serv->manager_pid
        ];
        $file=config('swoole.pidfile');
        file_put_contents($file,json_encode($array));
    }
    public function onConnect($serv,$fd,$from_id)
    {

    }
    public function onReceive($serv,$fd,$from_id,$data)
    {

    }
    public function onClose($serv,$fd,$from_id)
    {

    }
}