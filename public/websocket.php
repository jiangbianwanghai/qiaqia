<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->set("fd", "[]");

/**
 * swoole从1.7.9开始增加了对WebSocket的支持
 */

//创建服务器对象，监听9502端口
$server = new swoole_websocket_server("0.0.0.0", 9502);

/**
 * 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数。
 * $req 是一个Http请求对象，包含了客户端发来的握手请求信息
 * onOpen事件函数中可以调用push向客户端发送数据或者调用close关闭连接
 */
$server->on('open', function ($server, $req) use ($redis) {
    echo "connection open: {$req->fd}\n";
    $str = json_decode($redis->get("fd"), true);
    if ($str == "") {
        $str = [];
    }
    if (!in_array($req->fd, $str)) {
        array_push($str, $req->fd);
        $str = json_encode($str);
        $redis->set("fd", $str);
    }
});

/**
 * 当服务器收到来自客户端的数据帧时会回调此函数。
 *
 * $frame 是swoole_websocket_frame对象，包含了客户端发来的数据帧信息
 * onMessage回调必须被设置，未设置服务器将无法启动
 * 客户端发送的ping帧不会触发onMessage，底层会自动回复pong包
 * $frame 共有4个属性，分别是:
 * 1.$frame->fd，客户端的socket id，使用$server->push推送数据时需要用到。
 * 2.$frame->data，数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
 * 3.$frame->opcode，WebSocket的OpCode类型，可以参考WebSocket协议标准文档
 * 4.$frame->finish， 表示数据帧是否完整，一个WebSocket请求可能会分成多个数据帧进行发送
 * PS:$data 如果是文本类型，编码格式必然是UTF-8，这是WebSocket协议规定的
 */
$server->on('message', function ($server, $frame) use ($redis) {
    /**
     * 向websocket客户端连接推送数据，长度最大不得超过2M。
     * $fd 客户端连接的ID，如果指定的$fd对应的TCP连接并非websocket客户端，将会发送失败
     * $data 要发送的数据内容
     * $opcode，指定发送数据内容的格式，默认为文本。发送二进制内容$opcode参数需要设置为WEBSOCKET_OPCODE_BINARY_FRAME
     */
    $content = strip_tags($frame->data);
    echo "received message: {$frame->data}\n";
    if (!empty($content)) {
        $server->push($frame->fd, $content);
        $str = json_decode($redis->get("fd"), true);
        foreach ($str as $key => $value) {
            if ($frame->fd != $value) {
                echo "客户{$value}:" . $content . "\n";
                $server->push($value, "客户{$frame->fd}:" . $content);
            }
        }
    }
});

//设置关闭事件
$server->on('close', function ($server, $fd) use ($redis) {
    echo "connection close: {$fd}\n";
    $str   = json_decode($redis->get("fd"), true);
    $point = array_keys($str, $fd, true); //search key
    array_splice($str, $point['0'], 1); //delete array
    $str = json_encode($str);
    $redis->set("fd", $str);
});

//服务开启
$server->start();
