<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 21:57
 */

namespace Server;


class MajiangServer
{
    public $readyPlayers = array();
    public $players = array();
    public function __construct(){}
    public function onWorkerStart($worker){}
    public function onConnect($connection){}
    public function onMessage($connection, $message) {
        $connection->send("you just send: $message");
    }
    public function onClose($connection){}
    public function onWorkerStop($connection){}

}