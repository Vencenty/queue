<?php

$link = new mysqli('127.0.0.1', 'root', 'root','test');

$redis = new Redis;
$redis->connect('127.0.0.1', '6379');
$queue = 'goods_queue';




while(true) {

    if($redis->lLen($queue) == 0 )
    {
        echo "等待秒杀入库...".PHP_EOL;
        sleep(1);
        continue;
    }
    // 获取用户ID
    $value = $redis->rPop($queue);

    $args = explode('-', $value);
    $user_id = $args[0];
    $time = $args[1];

    $sql = <<<EOF
    INSERT INTO `queue` VALUES (null, '{$user_id}','{$time}');
EOF;
    $res = $link->query($sql);
    if(!$res) {
        echo $link->error.PHP_EOL;
        $redis->lPush($queue, $value);
    }

    if($redis->lLen($queue) == 0) {
        break;
    }
    $length = $redis->lLen($queue);
    echo "写入成功,用户ID：{$user_id}\tRedis:{$queue}的长度为:{$length}".PHP_EOL;

//    sleep(1);
}
echo "入库完毕".PHP_EOL;