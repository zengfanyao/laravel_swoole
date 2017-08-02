<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-7-30
 * Time: 下午9:21
 */

namespace App\Handlers;


class WsSwooleHandler
{
    public function __construct()
    {
    }

    public function onStart($serv)
    {
        $file=config('swoole.pidfile');
       file_put_contents($file,$serv->master_pid);
    }

    public function onConnect($serv, $fd, $from_id)
    {

    }

//    public function onHandshake(\swoole_http_request $request, \swoole_http_response $respons)
//    {
//        \Log::info('onHandshake');
//        \Log::info(json_encode($request));
//
//        $respons->status(101);
//        $respons->end();
//
//        return true;
//    }

    public function onOpen($serv,$request)
    {
       // \App\Logic\WsLogic::onOpen($serv,$request);
        // \Log::info("server: handshake success with fd{$request->fd}\n");
        // \Log::info(json_encode($request));
    }
    public function onTask($serv,$task_id,$from_id,$data)
    {
        \Log::info('process Task');
        sleep(10);
        return;
    }
    public function onFinish($serv,$task_id,$data)
    {
        \Log::info('Task OnFinish');
    }
    public function onMessage($serv,$frame)
    {
         \Log::info("receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n");
       // $serv->push($frame->fd, "this is server");
    }
    public function onClose($serv,$fd)
    {
        \Log::info(' echo "client {$fd} closed\n";');
    }

}