<?php
$arr = ['online' => 1, 'uid' => '1101'];
echo json_encode($arr);
$msg = '{"online":1,"uid":"1101"}';
echo $msg;
print_r(json_decode($msg, true));
die;
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->lPush('key1', '在吗？');
$redis->lPush('key1', '我是南京的客户');
$redis->lPush('key1', '想了解一下你们的产品');
$redis->lPush('key1', '这是我的手机号：15834324105');
