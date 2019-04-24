<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/4/23
 * Time: 10:34
 */

namespace Server;

class Hu
{
    const PATH_RIGHT =1;
    const PATH_PAIR = 2;
    const PATH_PENG = 3;
    const PATH_GANG = 4;
    const PATH_INDEPEND = 5;
    public $pairs = array();
    public $pros  = array();
    public $walkPaths = array();
    public $tilesCount;
    public function __construct($tiles)
    {
        $this->initPros($tiles);
        $this->initPairs();

    }

    public function tryHu(){
        $flag = false;
        foreach($this->pairs as $pairPro){ //循环对子
            $_tileCount = $this->tilesCount;
           $pairPath = $pairPro->getPaths();
           $keys = array_keys($_tileCount);
            if($_tileCount[$pairPro->val]>2){
                if('z'==$pairPro->type){//字牌
                    $pairPro->setPaths(array(self::PATH_INDEPEND));
                }else{
                    $pairPro->setPaths(array(self::PATH_RIGHT));
                }
            }else{
                array_splice($keys,array_search($pairPro->val,$keys),1);
            }
            $_tileCount[$pairPro->val] = $_tileCount[$pairPro->val] -2;
            $this->walkPaths = array();
            sort($keys);
            $this->getTilePaths($keys,null); //这里要注意 改写递归接收者需要重构
           foreach ($this->walkPaths as $path){
                $flag = $this->tryMatch($_tileCount,$path);
                if($flag == true){
                    print_r($_tileCount);
                    print_r($path);
                    break 2;
                }else{
                 }
            }
            $pairPro->setPaths($pairPath);

        }
        return $flag;
    }
    public function initPros($tiles){
        sort($tiles);
        $this->tilesCount = array_count_values($tiles);
        foreach ($this->tilesCount as $tile=>$count ){
            $tilePro = new Tile($tile,$count,$tile[0]);
            $this->pros[$tile] = $tilePro;
        }
    }
    public function initPairs(){
        foreach ($this->pros as $k=>$pro){
            if($pro->size >=2){
                if($pro->size==2){
                    if('z'==$k[0]){
                        $pro->setPaths(array(self::PATH_INDEPEND));
                    }else{
                        $pro->setPaths(array(self::PATH_RIGHT));
                    }
                }elseif ($pro->size==3){
                    if('z'==$k[0]){
                        $pro->setPaths(array(self::PATH_PENG));
                    }else{
                        $pro->setPaths(array(self::PATH_RIGHT,self::PATH_PENG));
                    }

                }elseif ($pro->size==4){
                    if('z'==$k[0]){
                        $pro->setPaths(array(self::PATH_GANG));
                    }else{
                        $pro->setPaths(array(self::PATH_RIGHT,self::PATH_PENG,self::PATH_GANG));
                    }
                }
                $this->pairs[] = $pro;
            }else{
                if('z'==$k[0]){
                    $pro->setPaths(array(self::PATH_INDEPEND));
                }else{
                    $pro->setPaths(array(self::PATH_RIGHT));
                }

            }

        }
    }

    /**
     * 尝试每一种路径
     * @param $tilesCount
     * @param $pathArr
     * @return bool
     */
    public function tryMatch($tilesCount,$pathArr){
        //先处理path为 peng的节点 变更其path为right
        $flag = true;
        foreach ($pathArr as $k=>$path){
            if($path==self::PATH_PENG){
                if($tilesCount[$k]==4){
                    $tilesCount[$k] = 1;
                    $pathArr[$k] = self::PATH_RIGHT;
                }else{
                    $tilesCount[$k] = 0;
                }
            }else if($path==self::PATH_GANG){
                $tilesCount[$k] = 0;
            }
        }
        //剩下来的path都是RIGHT和INDEPEND
        //print_r($tilesCount);
        foreach ($tilesCount as $key=>&$count){
            if($count==0){
                continue;
            }
            //print_r($pathArr);
            $curPath = $pathArr[$key];
            if($curPath==self::PATH_INDEPEND){
                $flag = false;
                break;
            }
            $nkey = $this->getNextKey($key);
            $nnkey = $this->getNextKey($nkey);
            //print_r($nkey); print_r($nnkey);
            if($nkey==null||$nnkey==null){
                 $flag = false;
                return $flag;
            }else if(!isset($tilesCount[$nkey])||!isset($tilesCount[$nnkey])){
                $flag = false;
                return $flag;
            }

            for($i=0;$i<$count;$i++){
                if($tilesCount[$key]>0&&$tilesCount[$nkey]>0&&$tilesCount[$nnkey]>0){
                    $tilesCount[$key]--;
                    $tilesCount[$nkey]--;
                    $tilesCount[$nnkey]--;
                }else{ //条件不满足
                    $flag = false;
                    break;
                }
            }
            if($flag==false){
                break;
            }
        }
       // echo 'match'; print_r($tilesCount);
        return $flag;
    }
    public function  getNextKey($key){
        if($key==null){
            return null;
        }
         if('0'< $key[1]&&$key[1]<'9'){
                return $key[0]. chr(ord($key[1])+1);
         }else {
                return null;
         }
    }
    public function getTilePaths($tiles,$tilePath){
        //$count =

        $tilePaths = $tilePath?$tilePath: array();
        if(count($tilePaths)==count($tiles)){
            $this->walkPaths[] = $tilePaths;
            return $tilePaths;
        }
        $index = count($tilePaths);
        //$result = array();
        foreach($this->pros[$tiles[$index]]->paths as $path){
                $tilePaths[$tiles[$index]] = $path;

                $ret = $this->getTilePaths($tiles,$tilePaths); //(1,2) (1.3)
                //$result[] = $ret;
               // $this->walkPaths[] = $ret;
                 array_pop($tilePaths);
        }
        //return $result;

    }
    public static function main(){
        $tiles1 = ['m1','m1','m1','m2','m2','m2','z1','z1'];
        $tiles2 = ['m1','m1','m1','m2','m2','m2','m2','m3','m3','m3','m4','m4','m4','m4'];
        $tiles3 = ['m1','m1','m1','p2','p3','p4','p9','p9','z1','z1','z1','z2','z2','z2'];
        $tiles4 = ['m1','m2','m3','m7','m7','s2','s2','s6','s7','s8','m7'];
        $tiles5 = ["m1","s8","s7","m2","s2","m7","m3","s2","s6","m7","m7"];
//        foreach ($tiles1 as $k=>&$tile){
//            print_r($tile);
//            print_r($tiles1[$k]);
//            $tiles1[3] = "zzz";
//        }
//        exit;
        //$stime=microtime(true);
        $hu = new Hu($tiles5);
       // $etime=microtime(true);
        //print_r($hu);
        print_r($hu->tryHu());
      //  $total=$etime-$stime;   #计算差值
        //echo "{$total} times";
        //$ret = $hu->getTilePaths(['m1','m2','m3','m4'],null);
        //print_r($hu->pros);
        //print_r($hu->walkPaths);
    }
}

class Tile
{
    public $isPeng = false;
    public $isGang = false;
    public $isPair = false;
    public $curPath = null;
    public $type = null;
    public $val = null;
    public $size= 0;
    public $paths = array();
    public function __construct($val,$size,$type)
    {
        $this->val = $val;
        $this->size = $size;
        $this->type = $type;
    }
    public function setPaths($paths){
        $this->paths = $paths;
    }

    public function  getPaths(){
        return $this->paths;
    }
    public function  setSize($size){
        $this->size = $size;
    }
}
Hu::main();
