<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-8-13
 * Time: 上午2:16
 */

namespace App\swoole\ChatRoom\Client;


class ChatRoom
{
    private $client;
    private $channel=0;
    private $online_list;
    public function __construct()
    {
        $this->client=new \swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);

        $this->client->on('Connect',array($this,'onConnect'));
        $this->client->on('Receive',array($this,'onReceive'));
        $this->client->on('Close',array($this,'onClose'));
        $this->client->on('Error',array($this,'onError'));
    }
    public function connect()
    {
        $fp=$this->client->connect('127.0.0.1',8888,1);
        if (!$fp)
        {
            echo "Error:{$fp->errMsg}";
            return;
        }
    }

    /**
     * @param $cli
     * @param $dataparam op user action
     * list user list
     * fd user offline
     */
    public function onReceive($cli,$data)
    {
        $param=json_decode(unpack('N/a*',$data)['1'],true);
        if ($param['op']=='online')
        {
            echo "new user {$param['name']} online!";
            $this->online_list[$param['fd']]=$param['name'];
        }else
        {

        }
    }
}
https://github.com/chenchaojie/Swoole/blob/master/ChatRoom/Client/chatroom.php