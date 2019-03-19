<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/20
 * Time: 0:38
 */

use \Workerman\Worker;
define('GLOBAL_START', true);
// socket服务端文件
require_once __DIR__ . '/start_worker.php';
// web服务
require_once __DIR__ . '/start_web.php';
Worker::runAll();