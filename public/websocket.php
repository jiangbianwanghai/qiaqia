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
        $khtofd = json_decode($redis->get("khtofd"), true);
        $fdtokh = json_decode($redis->get("fdtokh"), true);
        if (empty($khtofd[$data['uid']])) {
            $khtofd[$data['uid']] = $frame->fd;
            $redis->set("khtofd", json_encode($khtofd));
            //fd -> 帐号
            $fdtokh[$frame->fd] = $data['uid'];
            $redis->set("fdtokh", json_encode($fdtokh));
        }

        /**
         * 将新客户存入客户表
         */
        $kh = json_decode($redis->get("kh"), true); //读取客户表
        if (empty($kh[$data['uid']])) {
            $kh[$data['uid']] = [
                'uid'    => $data['uid'],
                'ua'     => $data['ua'],
                'avatar' => $avatar[mt_rand(0, 7)],
            ];
            $redis->set("kh", json_encode($kh));
        }

        /**
         * 将客户和客服建立关联
         * 前提是客服必须已经在线了，如果不在线是没有办法建立关系的
         */
        $khtokf = json_decode($redis->get("khtokf"), true);
        $kftofd = json_decode($redis->get("kftofd"), true);
        $kftokh = json_decode($redis->get("kftokh"), true);
        if (empty($khtokf[$data['uid']]) && !empty($kftofd)) {
            $kfid   = array_keys($kftofd);
            $random = mt_rand(0, count($kfid) - 1);
            $currkf = $kfid[$random];
            //khtokf
            $khtokf[$data['uid']] = $currkf;
            $redis->set("khtokf", json_encode($khtokf));
            //kftokh
            if (!isset($kftokh[$currkf])) {
                $kftokh[$currkf] = [$data['uid']];
                $redis->set("kftokh", json_encode($kftokh));
            } elseif (!in_array($data['uid'], $kftokh[$currkf])) {
                array_unshift($kftokh[$currkf], $data['uid']);
                $redis->set("kftokh", json_encode($kftokh));
            }
            //发送刷新左侧客户列表信号
            $flashKhMenu = json_encode(['from' => $data['uid'], 'to' => $currkf, 'op' => 'flash_kh_menu']);
            $server->push($kftofd[$currkf], $flashKhMenu);
        } else {
            //发送刷新左侧客户列表信号
            $flashKhMenu = json_encode(['from' => $data['uid'], 'to' => $khtokf[$data['uid']], 'op' => 'flash_kh_menu']);
            $server->push($kftofd[$khtokf[$data['uid']]], $flashKhMenu);
        }
        echo $flashKhMenu . PHP_EOL;
        $fdtokf = json_decode($redis->get("fdtokf"), true);
        if (empty($fdtokf)) {
            $pushMsg     = ['from' => 'system', 'msg' => '系统客服暂时没有上线，你可以先留言。'];
            $pushMsgJson = json_encode($pushMsg);
            $server->push($frame->fd, $pushMsgJson);
        }
    }

    /**
     * 获取消息
     */
    if (!empty($data['post'])) {
        $msg = strip_tags($data['msg']); //为了安全，过滤掉html和js标签

        //客户端发送消息给客服
        if ($data['role'] == 'kh') {
            $fdtokh = json_decode($redis->get("fdtokh"), true);
            $khtokf = json_decode($redis->get("khtokf"), true);
            $kftofd = json_decode($redis->get("kftofd"), true);
            $khtofd = json_decode($redis->get("khtofd"), true);
            $kh     = json_decode($redis->get("kh"), true); //读取客户表

            $currkh_uid = $fdtokh[$frame->fd];
            if (!empty($khtokf[$fdtokh[$frame->fd]])) {
                $currkf_uid = $khtokf[$fdtokh[$frame->fd]];
                $currkf_fd  = $kftofd[$currkf_uid]; //客服端fd
            } else {
                $currkf_uid = 'system';
                $currkf_fd  = '';
            }

            $pushMsg = [
                'op'     => 'send_msg',
                'from'   => $currkh_uid,
                'to'     => $currkf_uid,
                'avatar' => $kh[$currkh_uid]['avatar'],
                'me'     => 1,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ];
            $pushMsgJson = json_encode($pushMsg);
            echo $pushMsgJson . PHP_EOL;

            $key1 = $currkh_uid . ':' . $currkf_uid; //客户的聊天日志表的key
            $redis->lPush($key1, $pushMsgJson); //存入客户的聊天日志表
            $server->push($frame->fd, $pushMsgJson); //给客户端推送消息

            $pushMsg['me'] = 0;
            $pushMsgJson   = json_encode($pushMsg);

            $key2 = $currkf_uid . ':' . $currkh_uid; //客服的聊天日志表的key
            $redis->lPush($key2, $pushMsgJson);
            if ($currkf_fd) {
                $server->push($currkf_fd, $pushMsgJson); //给客服端推送消息
            }
        }

        //客服发送消息给客户
        if ($data['role'] == 'kf') {
            $fdtokf = json_decode($redis->get("fdtokf"), true);
            $kftokh = json_decode($redis->get("kftokh"), true);
            $khtofd = json_decode($redis->get("khtofd"), true);

            $currkf_uid = $fdtokf[$frame->fd];
            $currkh_uid = $data['khid'];

            $pushMsg = [
                'op'     => 'send_msg',
                'from'   => $currkf_uid,
                'to'     => $currkh_uid,
                'avatar' => 'avatar.jpg',
                'me'     => 1,
                'msg'    => $msg,
                'time'   => date("Y-m-d H:i:s"),
            ];
            $pushMsgJson = json_encode($pushMsg);
            echo $pushMsgJson . PHP_EOL;

            $key1 = $currkf_uid . ':' . $currkh_uid; //客服的聊天日志表的key
            $redis->lPush($key1, $pushMsgJson); //存入客服的聊天日志表
            $server->push($frame->fd, $pushMsgJson); //给客服端推送信息

            $pushMsg['me'] = 0;
            $pushMsgJson   = json_encode($pushMsg);
            $currkh_fd     = $khtofd[$currkh_uid]; //客户的fd
            $key2          = $currkh_uid . ':' . $currkf_uid; //客户的聊天日志表的Key
            $redis->lPush($key2, $pushMsgJson);
            $server->push($currkh_fd, $pushMsgJson); //给客户服端推送消息
        }
    }
});

$server->on('close', function ($server, $fd) use ($redis) {
    $fdtokf = json_decode($redis->get("fdtokf"), true);
    $kftofd = json_decode($redis->get("kftofd"), true);
    $fdtokh = json_decode($redis->get("fdtokh"), true);
    $khtofd = json_decode($redis->get("khtofd"), true);

    //客服退出时清理客服连接池
    if (!empty($fdtokf[$fd])) {
        $tmp = $fdtokf[$fd];
        unset($fdtokf[$fd]);
        unset($kftofd[$tmp]);
        $redis->set("fdtokf", json_encode($fdtokf));
        $redis->set("kftofd", json_encode($kftofd));
    }

    //客户退出时清理客户连接池
    if (!empty($fdtokh[$fd])) {
        $khtokf = json_decode($redis->get("khtokf"), true);
        $currkf = $kftofd[$khtokf[$fdtokh[$fd]]];
        //发送刷新左侧客户列表信号
        $flashKhMenu = json_encode(['from' => $fdtokh[$fd], 'op' => 'flash_kh_menu']);
        $server->push($currkf, $flashKhMenu);

        $tmp = $fdtokh[$fd];
        unset($fdtokh[$fd]);
        unset($khtofd[$tmp]);
        $redis->set("fdtokh", json_encode($fdtokh));
        $redis->set("khtofd", json_encode($khtofd));
    }

    echo "connection close: {$fd}\n";
});

//服务开启
$server->start();
