<?php

$clients = array();
$servers = array(
    '127.0.0.1',
    '127.0.0.1',
);
for ($i = 0; $i < count($servers); $i++) {

    if ($i == 0) {
        $port = ':9610';
    } else {
        $port = ':9620';
    }
    echo '转发服务器链接到消息服务器:' . $servers[$i] . $port . "\n";
    $clients[$servers[$i] . $port] = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
    $clients[$servers[$i] . $port]->remote_ip = $servers[$i];
    $clients[$servers[$i] . $port]->on("connect", function(swoole_client $cli) use ($i) {
        $data = array(
            'cmd'=>'register',
//            'name'=>$cli->remote_ip . '_router',
        );

        if ($i == 0) {
            $data['name'] = $cli->remote_ip . ':9610_router';
        } else {
            $data['name'] = $cli->remote_ip . ':9620_router';
        }

        echo "转发服务器链接消息服务器:" . json_encode($data) . "\n";
        $cli->send(json_encode($data));
        echo $cli->remote_ip . " Connect Success \n";
    });

    $clients[$servers[$i] . $port]->on("receive", function (swoole_client $cli, $data) {

        echo $data;
        $msg = (array) json_decode($data);
        echo '10:' . json_encode($msg);
        $remote_ip = $msg['recv_ip'];
        echo '11:' . json_encode($remote_ip);
        unset($msg['recv_ip']);
        global $clients;

        $clients[$remote_ip]->send(json_encode($msg));
    });
    $clients[$servers[$i] . $port]->on("error", function(swoole_client $cli) {
        echo "{$cli->remote_ip} error\n";
    });

    $clients[$servers[$i] . $port]->on("close", function(swoole_client $cli) {
        echo "{$cli->remote_ip} Connection close\n";
    });
//    $clients[$servers[$i]]->connect($servers[$i], 9501, 0.5);

    if ($i == 0) {
        $clients[$servers[$i] . $port]->connect($servers[$i], 9610, 0.5);
    } else {
        $clients[$servers[$i] . $port]->connect($servers[$i], 9620, 0.5);
    }

}