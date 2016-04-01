<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-08
 * Time: 11:34
 */

class IndexController extends Controller {
    private $_daytime;
    private $_daydate;
    private $_now_time;

    public function init() {
        parent::init();
        $this->_now_time = $_SERVER['REQUEST_TIME'];
        $this->_daytime = date("Y-m-d H:i:s", $this->_now_time);
        $this->_daydate = date("Y-m-d", $this->_now_time);
    }

    /**
     * 首页展示
     */
    public function indexAction(){
        $seocfg = array(
            'title'=> '用户登录-塑米城-塑料原料交易平台',
            'keywords'=> '塑米城，塑料米，塑料行业，塑料报价，塑料行情，塑料原料价格',
            'description'=> '用户登录-塑米城-塑料原料交易平台',
        );
        $this->_view->seocfg = $seocfg;
    }



}