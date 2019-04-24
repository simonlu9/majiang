<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/21
 * Time: 10:23
 */

namespace Server;


class Majiang
{
    static $TALES_NO = ['m1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7', 'm8', 'm9', //一万到九万
                                   'p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8', 'p9',//一筒到九筒
                                   's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9',//一条到九条
                                    'z1', 'z2', 'z3', 'z4', 'z5', 'z6', 'z7']; //东南西北白發中

    public static function shuffleTails(){
        $tails = array_merge(self::$TALES_NO,self::$TALES_NO,self::$TALES_NO,self::$TALES_NO);
        shuffle($tails);
        return $tails;
    }
    public static function isHu($tiles){

    }
    public static function checkIfPeng(Player $player,$tail){
        $tails = $player->tails;
        if(isset($tails[$tail])&&count($tails[$tail])>=2){
            return true;
        }
        return false;
    }

    /**
     * 检查手牌是否能杠
     * 明杠  其中有一个是别人的
     * 暗杠   全部都是自己摸的
     * @param Player $player
     * @param $tail
     * @return bool
     */
    public static function  checkIfGang(Player $player,$tail){
        $tails = $player->tails;
        if(isset($tails[$tail])&&count($tails[$tail])==3){
            return true;
        }else if(isset($player->pengTails[$tail])){
            return true;
        }
        return false;
    }
}

//$tails = Majiang::shuffleTails();
//print_r($tails);