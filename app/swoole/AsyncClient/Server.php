<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-8-13
 * Time: 上午2:01
 */

namespace App\swoole\AsyncClient;


class Server
{
    private $server=null;
    public function __construct()
    {
        $this->server=new \swoole_server("127.0.0.1",9501);
        $this->server->on('start',array($this,'onStart'));
        $this->server->on('connect',array($this,'onConnect'));
        $this->server->on('receive',array($this,'onReceive'));
        $this->server->on('close',array($this,'onClose'));
        $this->server->start();
    }
    public function onStart($serv)
    {
        echo "Start\n";
    }
    public function onConnect($serv,$fd,$from_id)
    {
        echo "Client {$fd} connect\n";
    }
    public function onReceive(\swoole_server $serv,$fd,$from_id,$data)
    {
        sleep(10);
        echo "Got message from Client {$fd}:{$data}\n";
        $this->server->send($fd,"hehehe");
    }
    public function onClose($serv,$fd,$from_id)
    {
        echo "Client {$fd} close connection\n";
    }

}
$server=new Server();