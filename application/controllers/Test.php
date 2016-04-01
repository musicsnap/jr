<?php
/**
 * Created by PhpStorm.
 * User: Windows
 * Date: 2016/1/28
 * Time: 16:26
 */
class TestController extends Controller {
    public function init() {
        parent::init();
    }

    /**
     *  测试Redis成功
     */
    public function indexAction(){
        $redis = new Redis_DbZero_Abstract();
        $arr = array(
            'code'=>'9',
            'msg'=>'哈哈'
        );
        $redis->set("tutorial11", json_encode($arr));
        echo "Stored string in redis:: " . $redis->get("tutorial11");

        $redis->lpush("tutorial-list", "Redis");
        $redis->lpush("tutorial-list", "Mongodb");
        $redis->lpush("tutorial-list", "Mysql");
        // 获取存储的数据并输出
        $arList = $redis->lrange("tutorial-list", 0 ,5);
        echo "Stored string in redis";
        print_r($arList);
        // 获取存储的数据并输出

        die();
    }

}