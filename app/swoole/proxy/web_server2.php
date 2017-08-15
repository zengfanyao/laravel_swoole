<?php
/**
 * @author: Leo|Zengyingle
 * @mail:   zengyingle@han-zi.cn
 * @mobile: 13570503483
 * @date:   2017年08月11日 星期五 16时02分53秒
 * @desc:
 */
class Server
{
    private $serv;
    private $redis;
    public function __construct()
    {

        /**
         * 初始化redis
         */
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);

        $this->serv = new swoole_websocket_server("0.0.0.0", 9621);

        $this->serv->on('message', array($this, 'onMessage')); // 接收websocket发送过来的数据
        $this->serv->on('Request', array($this, 'onRequest'));
        $this->serv->on('connect', array($this, 'onConnect')); // 链接设置

        $port1 = $this->serv->listen("0.0.0.0", 9620, SWOOLE_SOCK_TCP);

        $port1->set([
            'reactor_num' => 2, //reactor thread num
            'worker_num' => 4,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1,
        ]);
        $port1->on('Receive', array($this, 'onTcpReceive')); // 接收TCP发送过来的数据

        $this->serv->start();
    }
    //显示是哪个客户端发来的数据

    /**
     * 接受websocket 发送过来的数据
     * @param swoole_websocket_server $_server
     * @param $frame
     */
    public function onMessage(swoole_websocket_server $_server, $frame)
    {

        echo "\nMessage-----server: " . json_encode($_server) . "\n";
        echo "\nMessage-----frame: " . json_encode($frame) . "\n";
        $data = $frame->data;
        $fd = $frame->fd;
        $cmd = $data['cmd'];
        switch ($cmd) {

            case "register"://登陆
                //保存连接信息
                $save = array(
                    'fd' => $fd,
                    'socket_ip' => "127.0.0.1:9620"
                );
                $this->redis->set($data['name'], json_encode($save));
                echo "写入链接Key:" . $data['name'] . "\n";
                echo "写入链接:" . json_encode($save) . "\n";
                break;

            case "msg":
                $recv_id = $data['recv'];
                $recv = json_decode($this->redis->get($recv_id), true);
                echo '接收端的信息:' . json_encode($recv);
                if ($recv['socket_ip'] != "127.0.0.1:9620") {//发消息给proxy

                    // 转发服务器信息
                    $proxy = json_decode($this->redis->get('127.0.0.1:9620_router'));
                    //需要转发
                    $data['recv_ip'] = $recv['socket_ip'];
                    $_server->push($proxy['fd'], json_encode($data));
                } else {
                    //直接发送
                    $_server->push($recv['fd'], "{$data['send']}给您发了消息：{$data['content']}\n");
                }
                break;
        }
//        $_server->push($frame->fd, $frame->data);
    }
    //服务端接收到不同端口的数据如何处理
    public function onRequest($request, $response)
    {

    }

    /**
     * 链接处理
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(swoole_server $server,  $fd,  $from_id) {

    }

    /**
     * 接受TCP转发服务器发送过来的数据
     * @param swoole_server $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onTcpReceive( swoole_server $serv, $fd, $from_id, $data ) {

        echo "\nTcpReceive-----serv: " . json_encode($serv) . "\n";
        echo "\nTcpReceive-----fd: " . json_encode($fd) . "\n";
        echo "\nTcpReceive-----from_id: " . json_encode($from_id) . "\n";
        echo "\nTcpReceive-----data: " . json_encode($data) . "\n";

        echo '----------使用到websocket的message:----------' . "\n";
        $data = json_decode($data, true);
        $cmd = $data['cmd'];

        echo "redis 信息" . json_encode($this->redis) . "\n";
        switch ($cmd) {

            case "register"://登陆
                //保存连接信息
                $save = array(
                    'fd' => $fd,
                    'socket_ip' => "127.0.0.1:9620"
                );
                $this->redis->set($data['name'], json_encode($save));
                echo "写入链接Key:" . $data['name'] . "\n";
                echo "写入链接:" . json_encode($save) . "\n";
                break;

            case "msg":
                $recv_id = $data['recv'];
                $recv = json_decode($this->redis->get($recv_id), true);
                echo '接收端的信息:' . json_encode($recv);
                if ($recv['socket_ip'] != "127.0.0.1:9620") {//发消息给proxy

                    // 转发服务器信息
                    $proxy = json_decode($this->redis->get('127.0.0.1:9620_router'));
                    //需要转发
                    $data['recv_ip'] = $recv['socket_ip'];
                    $serv->send($proxy['fd'], json_encode($data));
                } else {
                    //直接发送
                    $serv->send($recv['fd'], "{$data['send']}给您发了消息：{$data['content']}\n");
                }
                break;
        }

//        $serv->send($fd, $data);
    }

}

new Server();