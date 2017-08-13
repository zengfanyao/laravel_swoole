<?php

/**
 * Created by PhpStorm.
 * User: yao
 * Date: 17/8/13
 * Time: 下午1:48
 */
class Client
{
    private $client;
    public function __construct()
    {
        $this->client=new \swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect()
    {
        if (!$fp = $this->client->connect('127.0.0.1', 9501, 1))
        {
            echo "Error : {$fp->errMsg}";
            return;
        }
        $msg_normal="this is a msg";
        $meg_eof="this is a msg\r\n";
        $msg_length=pack('N',strlen($msg_normal)).$msg_normal;
        fwrite(STDOUT,"enter a msg: ");
        $msg=trim(fgets(STDIN));
        $this->client->send($msg);
        $cli=$this->client;
        \swoole_event_add(STDIN,function($fp) use ($cli){
            fwrite(STDOUT,"Enter Msg:");
            $msg=trim(fgets(STDIN));
            $cli->send($msg);
        });
    }
}
$client=new Client();
$client->connect();