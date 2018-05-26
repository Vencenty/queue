<?php

$redis = new Redis;
$redis->connect('127.0.0.1', '6379');
$redis->flushAll();
$queue = 'goods_queue';


for ($i = 0; $i < 10000; $i++) {
    $len = $redis->lLen($queue);
    if($len >= 100) {
        $redis->close();
        break;
    }else{
        $user = rand(1000, 9999);
        $redis->lpush($queue, $user.'-'.microtime());

        echo "用户{$user}抢购成功了".PHP_EOL;
    }
}
echo "秒杀活动结束";

//$res = shell_exec("php mysql.php");

