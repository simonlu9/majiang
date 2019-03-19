<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 22:02
 */

namespace Server;


class Round
{
    public $id;
    /*玩家集合*/
    public $players = array();
    /*剩余牌数*/
    public $remainTailsNum = 0;
    public $southPlayer;
    /**/
    public $northPlayer;
    /*东家*/
    public $eastPlayer;
    /*西家*/
    public $westPayer;
    /*筛子*/
    public $dices;

    public $countdown;

    public function __construct()
    {
    }

    public function start(){

    }

    public function end(){

    }
}