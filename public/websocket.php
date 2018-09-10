<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

/**
 * 客服池（服务端启动需重置数据）
 *
 * 用于通过uid取到客服的fd
 *
 * 存储在线的客服uid和fd的对应关系
 * 例如：['1101'=>3]
 */
$redis->set("kftofd", "{}");

/**
 * 反向客服池（服务端启动需重置数据）
 *
 * 用于通过fd取到客服的uid
 *
 * 存储在线的客服uid和fd的对应关系
 * 例如：['3'=>'1101']
 */
$redis->set("fdtokf", "{}");

/**
 * 客户池（服务端启动需重置数据）
 *
 * 用于通过uid取到客户的fd
 *
 * 存储在线的客户uid和fd的对应关系
 * 例如：['uid_1536279503856'=>
 *     [
 *     'fd' => 15, //fd
 *     'ua' => "mozilla/5.0 (macintosh; intel mac os x 10.13; rv:62.0) firefox/62.0", //浏览器信息
 *     'avatar' => 'abc.jpg' //头像
 *     ]
 * ]
 */
$redis->set("khtofd", "{}");

/**
 * 反向客户池（服务端启动需重置数据）
 *
 * 用于通过fd取到客户的uid
 *
 * 存储在线的客户uid和fd的对应关系
 * 例如：['15'=> 'uid_1536279503856']
 */
$redis->set("fdtokh", "{}");

/**
 * 客户被分配给客服的关系池
 *
 * 例如：['uid_1536279503856'=>'1101']
 */
$redis->set("khtokf", "{}");

/**
 *客服分配的客户关系池
 *
 * 例如：['1101'=>'uid_1536279503856']
 */
$redis->set("kftokh", "{}");

$server = new swoole_websocket_server("0.0.0.0", 9502);

$server->on('open', function ($server, $req) use ($redis) {
    echo "connection open: {$req->fd}\n";
});

$server->on('message', function ($server, $frame) use ($redis) {

    /**
     * 解析传过来的json数据
     */
    $json = $frame->data;
    $data = json_decode($json, true);

    /**
     * 将客服的帐号与fd对应起来
     */
    if (!empty($data['kf'])) {
        $kftofd = json_decode($redis->get("kftofd"), true);
        $fdtokf = json_decode($redis->get("fdtokf"), true);
        //帐号 -> fd
        $kftofd[$data['uid']] = $frame->fd;
        $redis->set("kftofd", json_encode($kftofd));
        //fd -> 帐号
        $fdtokf[$frame->fd] = $data['uid'];
        $redis->set("fdtokf", json_encode($fdtokf));
    }

    /**
     * 将客户端的标识与fd对应起来
     */
    if (!empty($data['kh'])) {
        $avatar = ['vasu.jpg', 'sumit.jpg', 'sega.jpg', 'gan.jpg', 'chota.jpg', 'bhai.jpg', 'ajit.jpg', 'abc.jpg'];
        //帐号 -> fd
        $khtofd[$data['uid']] = [
            'fd'     => $frame->fd,
            'ua'     => $data['ua'],
            'avatar' => $avatar[mt_rand(1, 8)],
        ];
        $khtofd = json_encode($khtofd);
        $redis->set("khtofd", $khtofd);
        //fd -> 帐号
        $fdtokh[$frame->fd] = $data['uid'];
        $fdtokh             = json_encode($fdtokh);
        $redis->set("fdtokh", $fdtokh);
        //查询客户端是否映射到了客服
        $khtokf = json_decode($redis->get("khtokf"), true);
        $kftofd = json_decode($redis->get("kftofd"), true);
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
            $kftofd = json_decode($redis->get("kftofd"), true);
            $khtofd = json_decode($redis->get("khtofd"), true);
            echo $fdtokh[$frame->fd] . " to " . $khtokf[$fdtokh[$frame->fd]] . " " . $msg . PHP_EOL;

            $pushMsg = json_encode([
                'avatar' => $khtofd[$fdtokh[$frame->fd]]['avatar'],
                'me'     => 1,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key1 = $fdtokh[$frame->fd] . ':' . $khtokf[$fdtokh[$frame->fd]];
            $redis->lPush($key1, $pushMsg);
            $server->push($frame->fd, $pushMsg); //发给客户端信息
            echo $kftofd[$khtokf[$fdtokh[$frame->fd]]] . PHP_EOL;
            $pushMsg = json_encode([
                'avatar' => $khtofd[$fdtokh[$frame->fd]]['avatar'],
                'me'     => 0,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key2 = $khtokf[$fdtokh[$frame->fd]] . ':' . $fdtokh[$frame->fd];
            $redis->lPush($key2, $pushMsg);
            $server->push($kftofd[$khtokf[$fdtokh[$frame->fd]]], $pushMsg); //发给客服端消息
        }
        if ($data['role'] == 'kf') {
            $fdtokf = json_decode($redis->get("fdtokf"), true);
            $kftokh = json_decode($redis->get("kftokh"), true);
            $khtofd = json_decode($redis->get("khtofd"), true);
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
            echo $khtofd[$kftokh[$fdtokf[$frame->fd]]]['fd'] . PHP_EOL;
            $pushMsg = json_encode([
                'avatar' => 'avatar.jpg',
                'me'     => 0,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ]);
            $key2 = $kftokh[$fdtokf[$frame->fd]] . ':' . $fdtokf[$frame->fd];
            $redis->lPush($key2, $pushMsg);
            $server->push($khtofd[$kftokh[$fdtokf[$frame->fd]]]['fd'], $pushMsg); //发客户服端消息
        }
    }
});

$server->on('close', function ($server, $fd) use ($redis) {
    echo "connection close: {$fd}\n";
});

//服务开启
$server->start();
