<?php

class Controller extends Yaf_Controller_Abstract{

    //配置文件
    protected $_config;

    //Session
    protected $_session;

    /**
     * @var Db_MySQLi
     */
    protected $_db_handler;

    /**
     * @var Cache_Memcache
     */
    protected $_cache_handler;

    protected $_authManager;



    public function init(){
        $this->_config = Yaf_Registry::get("config");
        $this->_session = Yaf_Session::getInstance();
        $this->_session->start();

        //系统功能初始化
        $this->_db_handler = Yaf_Registry::get("db_handler");
        $this->_cache_handler = Yaf_Registry::get("cache_MemCache");
        $user_info = $this->_session->offsetGet('loginInfo');
        $this->_view->user_info =$user_info;


    }

}
