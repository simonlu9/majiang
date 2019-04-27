<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2019/4/25
 * Time: 16:11
 */


use Server\Hu;

class HuTest extends \PHPUnit_Framework_TestCase
{

    public function testTryHu()
    {
        $tiles1 = ['m1','m1','m1','m2','m2','m2','z1','z1'];
        $tiles2 = ['m1','m1','m1','m2','m2','m2','m2','m3','m3','m3','m4','m4','m4','m4'];
        $tiles3 = ['m1','m1','m1','p2','p3','p4','p9','p9','z1','z1','z1','z2','z2','z2'];
        $tiles4 = ['m1','m2','m3','m7','m7','s2','s2','s6','s7','s8','m7'];
        $tiles5 = ["m1","s8","s7","m2","s2","m7","m3","s2","s6","m7","m7"];
        $hu = new Hu($tiles5);
        $flag = $hu->tryHu();
        $this->assertEquals(true, $flag);
        //print_r($hu->walkPaths);

    }
}
