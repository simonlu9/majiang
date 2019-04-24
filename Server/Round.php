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
    const DESK_EAST = 0;
    const DESK_SOUTH = 1;
    const DESK_WEST = 2;
    const DESK_NORTH = 3;
    private static $autoIncrementId = 0;
    public $id;
    /*玩家集合*/
    public $players = array();
    /*玩家位置*/
    public $locationPlayers = array();
    /*剩余牌数*/
    public $tiles ;

    /*筛子*/
    public $dices;
    /*定位置*/
    public $starter;
     /*当前出牌指示*/
    public $playOrder = self::DESK_EAST;

    public function __construct(array $connections)
    {
        $this->id = ++self::$autoIncrementId;
        foreach ($connections as $connection){
            $player = new Player($connection,$this->id);
            $this->players[] = $player;
        }
    }

    /**
     * 开局分牌
     */
    public function start(){
        $this->tiles = Majiang::shuffleTails();
       $westPiece =$eastPiece = $northPiece = $southPiece =[];
       for($j=$i=0;$i<52;$j++){
           $eastPiece[] =  $this->tiles[$i++];
           $southPiece[] =  $this->tiles[$i++];
           $westPiece[] =  $this->tiles[$i++];
           $northPiece[] =  $this->tiles[$i++];
        }
        //$eastPiece[$j] = $this->tiles[$i];
        array_splice($this->tiles,0,52);
        $this->starter = rand(0,3);//确认起家
        $this->locationPlayers[self::DESK_EAST] = $this->players[$this->starter];
        $this->locationPlayers[self::DESK_SOUTH] = $this->players[ ($this->starter+1)%4];
        $this->locationPlayers[self::DESK_WEST] = $this->players[ ($this->starter+2)%4];
        $this->locationPlayers[self::DESK_NORTH] = $this->players[ ($this->starter+3)%4];

        $this->locationPlayers[self::DESK_EAST] ->start( $eastPiece,self::DESK_EAST);
        $this->locationPlayers[self::DESK_SOUTH] ->start($southPiece,self::DESK_SOUTH);
        $this->locationPlayers[self::DESK_WEST] ->start( $westPiece,self::DESK_WEST);
        $this->locationPlayers[self::DESK_NORTH] ->start( $northPiece,self::DESK_NORTH);
        $this->zimo();

    }

    /**
     * 摸牌动作
     * @param string $order
     */
    public function zimo($order="asc"){

        //$player = $this->locationPlayers[$this->playOrder];
        $tile = null;
        if($order=="asc"){
            $tile = array_shift($this->tiles);
        }else if($order=="desc"){
            $tile = array_pop($this->tiles);
        }
        if($tile==null){ //摸完了
            $this->jieju();
            return;
        }
        foreach ($this->locationPlayers as  $player){
            if($player->location==$this->playOrder){
                $player->catchTail($tile);
                //获取提示
                $notice = array();
                $tilesCount =  array_count_values($player->tiles);
                if($tilesCount[$tile]==4){ //暗杠
                    $notice = array('gang'=>$tile);
                }else if(in_array($tile,$player->pengTiles)){ //寻找明杠
                    $notice = array('buGang'=>$tile);
                }
                //判断有没有胡
                $hu = new Hu($player->tiles);
                $flag = $hu->tryHu();
                if($flag==true){
                    $notice['hu'] = true;
                }

                $msg = new Msg("zimo",array('tile'=>$tile,'location'=>$this->playOrder,'roundId'=>$this->id,'notice'=>$notice,'tilesCount'=>count($this->tiles)));

            }else{
                $msg = new Msg("zimo",array('tile'=>"pai",'location'=>$this->playOrder,'roundId'=>$this->id,'notice'=>array(),'tilesCount'=>count($this->tiles)));

            }

            
            $player->connection->send(json_encode($msg));

        }
        $this->playOrder= ($this->playOrder+1)%4;
    }
    public function peng($tile,$location){
        $currentPlayer = $this->locationPlayers[$location];
        $currentPlayer->peng($tile);
        $lastPlayOrder = ($this->playOrder-1+4)%4;
        $msg = new Msg('peng',array('tile'=>$tile,'location'=>$location,'fromLocation'=>$lastPlayOrder));
        $playOrderMsg = new Msg('playOrder',array('location'=> $location));
        $this->playOrder = $location;
        foreach ($this->locationPlayers as $k=>$player){
            $player->connection->send(json_encode($msg));
             $player->connection->send(json_encode($playOrderMsg));
        }
        $this->playOrder= ($this->playOrder+1)%4;

    }

    /**
     * 暗杠
     * @param $tile
     * @param $location
     */
    public function gang($tile,$location){
        $currentPlayer = $this->locationPlayers[$location];
        $currentPlayer->gang($tile);
        $msg = new Msg('gang',array('tile'=>$tile,'location'=>$location));
        foreach ($this->locationPlayers as $k=>$player){
            $player->connection->send(json_encode($msg));
        }
        $this->playOrder = $location;
        $this->zimo("desc");
    }

    /**
     * 补杠
     * @param $tile
     * @param $location
     * @param $fromLocation
     */
    public function buGang($tile,$location,$fromLocation){
        $currentPlayer = $this->locationPlayers[$location];
        $currentPlayer->buGang($tile,$fromLocation);
        $msg = new Msg('buGang',array('tile'=>$tile,'location'=>$location,'fromLocation'=>$fromLocation));
        foreach ($this->locationPlayers as $k=>$player){
            $player->connection->send(json_encode($msg));
        }
        $this->playOrder = $location;
        $this->zimo("desc");
    }

    /**
     * 过
     * @param $location
     */
    public function pass($location){
       $this->zimo();
    }

    /**
     * 食胡
     * @param $location
     * @throws \Exception
     */
    public function hu($location){
        $currentPlayer = $this->locationPlayers[$location];
        //判断有没有胡
        $hu = new Hu($currentPlayer->tiles);
        $flag = $hu->tryHu();
        if(!$flag){
            throw new \Exception("食胡未满足条件");
        }
        $this->jieju();
    }

    /**
     * 打牌（要重构）
     * @param $location
     * @param $tile
     */
    public function play($tile,$location){
        $currentPlayer = $this->locationPlayers[$location];
        $currentPlayer->play($tile);
        $ifPengOrGang = false;
        foreach ($this->locationPlayers as $k=>$player){
            if($k!=$location){
                $notice = array();
               $tilesCount =  array_count_values($player->tiles);
                 if(isset($tilesCount[$tile])&&$tilesCount[$tile]>=2){ //可碰
                   $ifPengOrGang = true;
                   if($tilesCount[$tile]==2){
                       $notice = array('peng'=>$tile);

                   }else if($tilesCount[$tile]==3){ //可明杠
                     $notice = array('peng'=>$tile,'buGang'=>$tile);

                   }
               } //没有碰和杠情况，轮到下一位出牌
                   $msg = new Msg('play',array('tile'=>$tile,'location'=>$location,'notice'=>$notice));
                    $player->connection->send(json_encode($msg));

            }else{
                $msg = new Msg('play',array('tile'=>$tile,'location'=>$location,'notice'=>array()));
                $player->connection->send(json_encode($msg));
            }
        }
        if($ifPengOrGang){

        }else{ //出牌指示
            $this->zimo();
        }

    }

    /**
     * 结局处理
     * @param bool $winner
     */
    public function jieju($winner=false){
        $tiles = array();
        foreach ($this->locationPlayers as $k=>$player){
            $tiles[$k] = $player->tiles;
        }
        foreach ($this->locationPlayers as $k=>$player){
            $msg = new Msg('jieju',array('tiles'=> $tiles));
            //print_r($msg->data);
            $player->connection->send(json_encode($msg));
        }
    }

    /**
     * 出牌指示器
     * @param $playerId
     * @param $tail
     */
    public function playMonitor($playerId,$tail){
        $nexPlayer = null;
        foreach ($this->players as $id=>$player){
            if($id!==$playerId){
                /*检查其他是否碰*/
                $ifPeng = Majiang::checkIfPeng($player,$tail);
                if($ifPeng){
                    $nexPlayer = $player;
                    break;
                }
            }
        }
        if( $nexPlayer==null){

        }
    }


}