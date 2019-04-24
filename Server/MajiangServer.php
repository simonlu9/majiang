<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/19
 * Time: 21:57
 */

namespace Server;
use Workerman\Connection\TcpConnection;

class MajiangServer
{
    const CONN_PREFIX = "majiang_";
    const ROUND_PREFIX = "round_";
    public $readyPlayers = array();
    public $freePlayers = array();
    public $rounds  = array();
    public function __construct(){}
    public function onWorkerStart($worker){}
    public function onConnect(TcpConnection $connection){
        $this->freePlayers[self::CONN_PREFIX.$connection->id] = $connection;
    }
    public function onMessage(TcpConnection $connection, $message) {
        $messageData = json_decode($message, true);
        switch($messageData['type'])
        {
            case 'ready': /*准备*/
                 $this->readyHandler($connection);
                break;
            case 'play':   /*出牌*/
                $this->playHandler($connection,$messageData['data']);
                break;
            case 'peng':/*碰牌*/
                $this->pengHandler($connection,$messageData['data']);
                break;
            case 'gang':/*杠牌*/
                $this->gangHandler($connection,$messageData['data']);
                break;
            case 'buGang':/*补杠牌*/
                $this->buGangHandler($connection,$messageData['data']);
                break;
            case 'pass':/*过*/
                $this->passHandler($connection,$messageData['data']);
                break;
            case 'hu':/*胡牌*/
                $this->huHandler($connection,$messageData['data']);
                break;

        }
    }
    public function onClose($connection){
        unset($this->freePlayers[self::CONN_PREFIX.$connection->id]);
    }
    public function onWorkerStop($connection){

    }

    /**
     * @param TcpConnection $connection
     */
    public function  readyHandler(TcpConnection $connection){
        unset($this->freePlayers[self::CONN_PREFIX.$connection->id] );
        array_push($this->readyPlayers,$connection);
        if(count($this->readyPlayers)<4){
            $connection->send(json_encode(new Msg("ready","success")));
        }else{
            while (count($this->readyPlayers)>=4){
                $connections = array_splice($this->readyPlayers,0,4);
                $round = new Round($connections);
                $round->start();
                $this->rounds[self::ROUND_PREFIX.$round->id] = $round;
            }
        }


    }
    public function  playHandler(TcpConnection $connection,$msgData){
        $tile = $msgData['tile'];
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->play($tile,$location);

    }
    public function pengHandler(TcpConnection $connection,$msgData){
        $tile = $msgData['tile'];
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->peng($tile,$location);
    }
    public function gangHandler(TcpConnection $connection,$msgData){
        $tile = $msgData['tile'];
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->gang($tile,$location);
    }
    public function buGangHandler(TcpConnection $connection,$msgData){
        $tile = $msgData['tile'];
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $fromLocation = $msgData['fromLocation'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->buGang($tile,$location,$fromLocation);
    }
    public function passHandler(TcpConnection $connection,$msgData){
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->pass($location);
    }
    public function huHandler(TcpConnection $connection,$msgData){
        $roundId = $msgData['roundId'];
        $location = $msgData['location'];
        $round = $this->rounds[self::ROUND_PREFIX.$roundId];
        $round->hu($location);
    }

    public static function  sendMessage(TcpConnection $connection,$msg){
        $connection->send(json_encode($msg));
    }

}