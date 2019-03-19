<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/18
 * Time: 22:21
 */
use \Workerman\WebServer;
use \Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/vendor/autoload.php';
$web = new WebServer("http://0.0.0.0:8787");
$web->count = 2;
$web->name = 'MajiangWeb';
$web->addRoot('localhost', __DIR__.'/Web');
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}