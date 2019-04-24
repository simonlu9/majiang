<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 21:55
 */

namespace Server;


use Workerman\Connection\TcpConnection;

class Player
{
    private static $autoIncrementId = 0;
    /*玩家ID*/
    public $id;
    /*玩家名字*/
    public $name;
    /*1.准备状态 2.悠闲状态 3玩牌中*/
    public $status;
    /*玩家位置*/
    public $location;
    /* socket链接 */
    public $connection;
    /*持牌*/
    public $tiles = array();
    /*碰牌*/
    public $pengTiles = array();
    /*杠牌*/
    public $gangTiles= array();
    /*补杠牌*/
    public $buGangTiles = array();
    /*房间ID*/
    public $roundId;


    public function __construct(TcpConnection $connection,$roundId){
        $this->id = ++self::$autoIncrementId;
        $this->connection = $connection;
        $this->roundId = $roundId;
    }

    /**
     * @param $tiles
     * @param $location
     */
    public function start($tiles,$location){
        $this->location = $location;
        $this->tiles = $tiles;
        $msg = new Msg("kaiju",array('tiles'=>$tiles,'location'=>$location,'roundId'=>$this->roundId));
        $this->connection->send(json_encode($msg));
    }
    public function play($tile){
        if(in_array($tile,$this->tiles)){
            array_splice($this->tiles,array_search($tile,$this->tiles),1);
        }
   }

    /**
     * 碰操作
     * @param $tile
     */
   public function peng($tile){
      $tilesCount =  array_count_values($this->tiles);
       if($tilesCount[$tile]>=2){ //可碰
           for($i=0; $i<2; $i++){
               if(in_array($tile,$this->tiles)){
                   array_splice($this->tiles,array_search($tile,$this->tiles),1);
               }
           }
           array_push($this->pengTiles,$tile);
       }

   }


    /**
     * 暗杠操作
     * @param $tile
     * @throws \Exception
     */
   public function gang($tile){
       //暗杠
       $tilesCount =  array_count_values($this->tiles);
       if($tilesCount[$tile]==4){
           for($i=0; $i<4; $i++){
               if(in_array($tile,$this->tiles)){
                   array_splice($this->tiles,array_search($tile,$this->tiles),1);
               }
           }
           array_push($this->gangTiles,$tile);

        }else{
           throw new \Exception("不允许暗杠操作，条件不满足");
       }
   }

    /**
     * 补杠
     * @param $tile
     * @throws \Exception
     */
    public function buGang($tile,$fromLocation){
        if($this->location!=$fromLocation){ //有三只
            $tilesCount =  array_count_values($this->tiles);
            if($tilesCount[$tile]==3){
                for($i=0; $i<3; $i++){
                    if(in_array($tile,$this->tiles)){
                        array_splice($this->tiles,array_search($tile,$this->tiles),1);
                    }
                }
                array_push($this->buGangTiles,$tile);
            }else{
                print_r($this);print_r($fromLocation);
                throw new \Exception("不允许明杠操作，条件不满足");
            }
        }else{
            if(in_array($tile,$this->pengTiles)&&in_array($tile,$this->tiles)){
                if(in_array($tile,$this->tiles)){
                    array_splice($this->tiles,array_search($tile,$this->tiles),1);
                }
                array_push($this->buGangTiles,$tile);
            }else{
                throw new \Exception("不允许明杠操作，条件不满足");
            }
        }

    }

    public function catchTails($tile){
        $msg = new Msg("kaiju",$tile);
        $this->connection->send(json_encode($msg));
    }
    public function catchTail($tile){
        array_push($this->tiles,$tile);
    }
    public function tiles2Map(){

    }
    public function onWorkerStart($worker){}
    public function onConnect($connection){}
    public function onMessage($connection, $message) {}
    public function onClose($connection){}
    public function onWorkerStop($connection){}
}