<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/3/21
 * Time: 14:30
 */

namespace Server;


class Msg
{
    public $type;
    public $data;
    public function __construct($type,$data=null)
    {
        $this->type = $type;
        $this->data = $data;
    }



}