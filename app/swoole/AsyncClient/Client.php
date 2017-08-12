<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-8-13
 * Time: 上午1:52
 */

namespace App\swoole\AsyncClient;


class Client
{
    private $client;
    public function __construct()
    {
        $this->client=new \swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $this->client->on('Connect',array($this,'OnConnect'));
        $this->client->on('Receive',array($this,'OnReceive'));
        $this->client->on('Close',array($this,'OnClose'));
        $this->client->on('Error',array($this,'OnError'));
    }
    public function connect()
    {
        $fp=$this->client->connect("127.0.0.1",9501,1);
        if (!$fp)
        {
            echo "Error:{$fp->errMsg}[{$fp->errCode}]\n";
            return;
        }
    }
    public function onReceive($cli,$data)
    {
        echo "Get Message From server:{$data}\n";
    }
    public function onConnect($cli)
    {
        fwrite(STDOUT,"Enter Msg:");
        swoole_event_add(STDIN,function($fp){
            global $cli;
            fwrite(STDOUT,"Enter Msg:");
            $msg=trim(fgets(STDIN));
            $cli->send($msg);
        });
    }

    public function onClose($cli)
    {
        echo "Client close connection \n";
    }
    public function onError()
    {

    }
    public function send($data)
    {
        $this->client->send($data);
    }
    public function isConnected()
    {
        return $this->client->isConnected();
    }

}
$cli=new Client();
$cli->connect();