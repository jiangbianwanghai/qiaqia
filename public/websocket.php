<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$redis->set("fd", "[]"); //存客户端

$redis->set("kfid", "[]"); //存客服端标识
$redis->set("khid", "[]"); //存客户端标识
$redis->set("fdtokf", "[]"); //fd到客服标识映射
$redis->set("fdtokh", "[]"); //fd到客户端标识映射
$redis->set("khtokf", "[]"); //客户端到客服端的映射
$redis->set("kftokh", "[]"); //客服端到客户端的映射

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
    $json = $frame->data;
    $data = json_decode($json, true);

    /**
     * 将客服的帐号与fd对应起来
     */
    if (!empty($data['kf'])) {
        //帐号 -> fd
        $kfid[$data['uid']] = $frame->fd;
        $kfid               = json_encode($kfid);
        $redis->set("kfid", $kfid);
        //fd -> 帐号
        $fdtokf[$frame->fd] = $data['uid'];
        $fdtokf             = json_encode($fdtokf);
        $redis->set("fdtokf", $fdtokf);
    }

    /**
     * 将客户端的标识与fd对应起来
     */
    if (!empty($data['kh'])) {
        $avatar = ['vasu.jpg', 'sumit.jpg', 'sega.jpg', 'gan.jpg', 'chota.jpg', 'bhai.jpg', 'ajit.jpg', 'abc.jpg'];
        //帐号 -> fd
        $khid[$data['uid']] = [
            'fd'     => $frame->fd,
            'ua'     => $data['ua'],
            'avatar' => $avatar[mt_rand(1, 8)],
        ];
        $khid = json_encode($khid);
        $redis->set("khid", $khid);
        //fd -> 帐号
        $fdtokh[$frame->fd] = $data['uid'];
        $fdtokh             = json_encode($fdtokh);
        $redis->set("fdtokh", $fdtokh);
        //查询客户端是否映射到了客服
        $khtokf = json_decode($redis->get("khtokf"), true);
        $kfid   = json_decode($redis->get("kfid"), true);
        if (empty($khtokf[$data['uid']])) {
            $khtokf[$data['uid']] = '1101';
            $khtokf               = json_encode($khtokf);
            $redis->set("khtokf", $khtokf);
            $kftokh['1101'] = $data['uid'];
            $kftokh         = json_encode($kftokh);
            $redis->set("kftokh", $kftokh);
        }
    }

    /**
     * 获取消息
     */
    if (!empty($data['post'])) {
        $msg = strip_tags($data['msg']);
        //客户端发送消息给客服
        if ($data['role'] == 'kh') {
            $fdtokh = json_decode($redis->get("fdtokh"), true);
            $khtokf = json_decode($redis->get("khtokf"), true);
            $kfid   = json_decode($redis->get("kfid"), true);
            $khid   = json_decode($redis->get("khid"), true);
            echo $fdtokh[$frame->fd] . " to " . $khtokf[$fdtokh[$frame->fd]] . " " . $msg . PHP_EOL;

            $pushMsg = json_encode([
                'avatar' => $khid[$fdtokh[$frame->fd]]['avatar'],
                'me'     => 1,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key1 = $fdtokh[$frame->fd] . ':' . $khtokf[$fdtokh[$frame->fd]];
            $redis->lPush($key1, $pushMsg);
            $server->push($frame->fd, $pushMsg); //发给客户端信息
            echo $kfid[$khtokf[$fdtokh[$frame->fd]]] . PHP_EOL;
            $pushMsg = json_encode([
                'avatar' => $khid[$fdtokh[$frame->fd]]['avatar'],
                'me'     => 0,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key2 = $khtokf[$fdtokh[$frame->fd]] . ':' . $fdtokh[$frame->fd];
            $redis->lPush($key2, $pushMsg);
            $server->push($kfid[$khtokf[$fdtokh[$frame->fd]]], $pushMsg); //发给客服端消息
        }
        if ($data['role'] == 'kf') {
            $fdtokf = json_decode($redis->get("fdtokf"), true);
            $kftokh = json_decode($redis->get("kftokh"), true);
            $khid   = json_decode($redis->get("khid"), true);
            echo $fdtokf[$frame->fd] . " to " . $kftokh[$fdtokf[$frame->fd]] . " " . $msg . PHP_EOL;
            $pushMsg = json_encode([
                'avatar' => 'avatar.jpg',
                'me'     => 1,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key1 = $fdtokf[$frame->fd] . ':' . $kftokh[$fdtokf[$frame->fd]];
            $redis->lPush($key1, $pushMsg);
            $server->push($frame->fd, $pushMsg); //发给客服端信息
            echo $khid[$kftokh[$fdtokf[$frame->fd]]]['fd'] . PHP_EOL;
            $pushMsg = json_encode([
                'avatar' => 'avatar.jpg',
                'me'     => 0,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key2 = $kftokh[$fdtokf[$frame->fd]] . ':' . $fdtokf[$frame->fd];
            $redis->lPush($key2, $pushMsg);
            $server->push($khid[$kftokh[$fdtokf[$frame->fd]]]['fd'], $pushMsg); //发客户服端消息
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
