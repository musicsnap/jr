<?php
class Db_Base
{
    /**
     *
     * 配置文件
     * @var
     */
    protected $_config;

    /**
     * @var Db_MySQLi
     */
    protected $_db_handler;

    /**
     * @var Cache_Memcache
     */
    protected $_cache_handler;

    /**
     * Construct
     *
     * @return void
     */

    public function __construct() {
		$this->_config = Yaf_Registry::get("config");

		$this->_db_handler = Yaf_Registry::get("db_handler");
        $this->_cache_handler = Yaf_Registry::get("cache_MemCache");

        $this->_session = Yaf_Session::getInstance();

    }

}