<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/13
 * Time: 下午1:59
 */
class Server
{
    private $_server=null;
    private $_n;
    public function __construct()
    {
        $this->_server=new \swoole_server('127.0.0.1',9501);
        $this->_server->set([
            'worker_num'         => 4,
            'daemonize'          => false,
            'max_request'        => 2000,
            'dispatch_mode'      => 2,
            'package_max_length' => 8192,
           // 'open_eof_check'     => true,
           // 'package_eof'        => "\r\n"
        ]);
        $this->_server->on('Start',array($this,'onStart'));
        $this->_server->on('workerstart',array($this,'onWorkerStart'));
        $this->_server->on('Connect',array($this,'onConnect'));
        $this->_server->on('Receive',array($this,'onReceive'));
        $this->_server->on('Close',array($this,'onClose'));
        $this->_server->start();

    }

    public function onStart($serv)
    {
        echo "master Start \n";
    }
    public function onWorkerStart($serv,$worker_id)
    {
        $this->_n=100;
    }

    public function onConnect($serv,$fd,$from_id)
    {
        echo "client {$fd} connect\n";
    }
    public function onReceive($serv,$fd,$from_id,$data)
    {
        echo "recv";
        \swoole_async_write('test.log',$data,-1,function($filename,$writlen){
            sleep(5);
            echo "filename:{$filename},{$writlen} byte";
        });
    }
    public function onClose($serv,$fd,$from_id)
    {
        echo "client {$fd} close connection\n";
    }
}
new Server();