<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$key  = '1101:uid_1536279503856';
$size = $redis->lSize($key);
for ($i = -1; $i >= -10; $i--) {
    print_r(json_decode($redis->lGet($key, $i), true));
}

//echo $size;
/*$redis->lPush('key1', '在吗？');
$redis->lPush('key1', '我是南京的客户');
$redis->lPush('key1', '想了解一下你们的产品');
$redis->lPush('key1', '这是我的手机号：15834324105');*/
