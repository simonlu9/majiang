<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/4/23
 * Time: 9:38
 */

namespace Server;


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