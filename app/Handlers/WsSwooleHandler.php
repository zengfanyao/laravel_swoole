<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-7-30
 * Time: 下午9:21
 */

namespace App\Handlers;


use Swoole\Table;

class WsSwooleHandler
{
    public function __construct()
    {
    }

    public function onStart($serv)
    {
      \Log::info('OnStart start');
    }
    public function onWokerStart($serv,$worker_id)
    {
        \Log::info('OnWorkerStart');

    }

    public function onConnect($serv, $fd, $from_id)
    {

    }

    public function onTimer($serv,$interval)
    {
        switch ($interval)
        {
            case "500":
                \Log::info('Do Thing A at Interval 500\n');
                break;
            case "1000":
                \Log::info('Do Thing B at Interval 500\n');
                break;
            case "15000":
                \Log::info('Do Thing C at Interval 500\n');
                break;
        }
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
        \Log::info("This Task {$task_id} from Worker {$from_id}\n");
        \Log::info("Data: {$data}\n");
        for($i=0;$i<10;$i++)
        {
            sleep(1);
            \Log::info("Task {$task_id} Handle {$i} times ... \n");
            $fd=json_decode($data,true)['fd'];
            $serv->send($fd,"Data in Task [$task_id]");
            return "Task {$task_id} result";
        }
   
    }
    public function onFinish($serv,$task_id,$data)
    {
       \Log::info("Task {$task_id} finish\n");
       \Log::info("Result: {$data}\n");
    }
    public function onMessage($serv,$frame)
    {
         \Log::info("receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n");
        $param=[
            'fd'=>$frame->fd
        ];
        $serv->task(json_encode($param));
        \Log::info('Continue Handle Worker');

       // $serv->push($frame->fd, "this is server");
    }
    public function onClose($serv,$fd)
    {
        \Log::info(' echo "client {$fd} closed\n";');
    }

}