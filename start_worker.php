<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 12:09
 */
use \Workerman\Worker;
use \Server\MajiangServer;
// 自动加载类
require_once __DIR__ . '/vendor/autoload.php';
$worker = new Worker('Websocket://0.0.0.0:8000');
$worker->name = 'MajiangWorker';
$majiangServer = new MajiangServer();
$worker->onWorkerStart = array($majiangServer, 'onWorkerStart');
$worker->onConnect     = array($majiangServer, 'onConnect');
$worker->onMessage     = array($majiangServer, 'onMessage');
$worker->onClose       = array($majiangServer, 'onClose');
$worker->onWorkerStop  = array($majiangServer, 'onWorkerStop');
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}