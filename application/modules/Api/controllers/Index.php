<?php
/**
 * Created by PhpStorm.
 * User: Talent Gao
 * Date: 14-8-26
 * Time: 下午4:07
 */
class IndexController extends Controller{
    public function init(){}

    public function indexAction(){
        $service = new Yar_Server(new Services_UserService());
        $service->handle();
        die;
    }

    public function searchAction(){
        echo "=================IndexSearch";

        die();
    }
}
