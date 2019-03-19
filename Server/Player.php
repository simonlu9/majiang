<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 21:55
 */

namespace Server;


class Player
{
    /*玩家ID*/
    public $id;
    /*玩家名字*/
    public $name;
    /*1.准备状态 2.悠闲状态 3玩牌中*/
    public $status;
    public function __construct(){}
    public function onWorkerStart($worker){}
    public function onConnect($connection){}
    public function onMessage($connection, $message) {}
    public function onClose($connection){}
    public function onWorkerStop($connection){}
}