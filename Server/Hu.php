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
            sort($keys);
            $walkPaths = $this->getTilePaths($keys,null); //已重构
           foreach ($walkPaths as $path){
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
        $tilePaths = $tilePath?$tilePath: array();
        if(count($tilePaths)==count($tiles)){
          //  $this->walkPaths[] = $tilePaths;
            return $tilePaths;
        }
        $index = count($tilePaths);
        $result = array();
        foreach($this->pros[$tiles[$index]]->paths as $path){
                $tilePaths[$tiles[$index]] = $path;

                $retArr = $this->getTilePaths($tiles,$tilePaths); //(1,2) (1.3)
                if (count($retArr) == count($retArr, 1)) {
                    $result[] = $retArr;
                }else{
                    foreach ($retArr as $ret){
                        $result[] = $ret;
                    }
                }
                array_pop($tilePaths);
        }
        return $result;

    }

}
