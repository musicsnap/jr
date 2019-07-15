<?php
/**
 * @name Bootstrap
 * @author {&$AUTHOR&}
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-Abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    private $_config;

    public function _initConfig() {
		//把配置保存起来
        $this->_config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $this->_config);
	}

    public function _initLocalName() {
        Yaf_Loader::getInstance()->registerLocalNamespace(array(
            'Smarty','Swift','Munee'
        ));
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {

        /**
         * register Routes plugin
         */
        $routes = new RoutesPlugin();
        $dispatcher->registerPlugin($routes);

        /**
         * register Smarty plugin
         */
        $smarty = new SmartyPlugin();
        $dispatcher->registerPlugin($smarty);

    }

    public function _initErrors(){
        if($this->_config->application->showErrors){
            error_reporting (-1);
            define('DEBUG_MODE', false);
            //报错是否开启
            ini_set('display_errors','On');
        }else{
            error_reporting (-1);
            define('DEBUG_MODE', false);
            ini_set('display_errors', 'Off');
        }
    }

    //初始化zend_db
    public function _initZendDbAdapter(){
        $dbAdapter = new Zend_Db_Adapter_Mysqli(
            $this->_config->database->zend->toArray()
        );

        $dbAdapter->query("SET NAMES {$this->_config->database->zend->charset}");

        Zend_Db_Table::setDefaultAdapter($dbAdapter);
    }
    //这个是初始化db类、
    public function _initDatabase(){
        //$servers = array();
        $database = $this->_config->database;
        $servers = $database->config->toArray();
        if(!empty($servers)){
            $_db_handler = new Db_MySQLi($servers);
            Yaf_Registry::set("db_handler", $_db_handler);
        }
        //从这边分开
        $servers[] = $database->master->toArray();
        $slaves = $database->slaves;
        if (!empty($slaves))
        {
            $slave_servers = explode('|', $slaves->servers);
            $slave_users = explode('|', $slaves->users);
            $slave_passwords = explode('|', $slaves->passwords);
            $slave_databases = explode('|', $slaves->databases);
            $slaves = array();
            foreach ($slave_servers as $key => $slave_server)
            {
                if (isset($slave_users[$key]) && isset($slave_passwords[$key]) && isset($slave_databases[$key]))
                {
                    $slaves[] = array('server' => $slave_server, 'user' => $slave_users[$key], 'password' => $slave_passwords[$key], 'database' => $slave_databases[$key]);
                }
            }
            $servers[] = $slaves[array_rand($slaves)];
        }
        Yaf_Registry::set('database', $servers);
        if (isset($database->mysql_cache_enable) && $database->mysql_cache_enable && !defined('MYSQL_CACHE_ENABLE'))
        {
            define('MYSQL_CACHE_ENABLE', true);
        }
        if (isset($database->mysql_log_error) && $database->mysql_log_error && !defined('MYSQL_LOG_ERROR'))
        {
            define('MYSQL_LOG_ERROR', true);
        }
        Yaf_Loader::import(APPLICATION_PATH . '/library/Db/Db.php');
        Yaf_Loader::import(APPLICATION_PATH . '/library/Db/DbQuery.php');
    }

    public function _initMemCache(){
        //$servers = array();
        $MemCache = $this->_config->memcached;
        $servers = $MemCache->config->toArray();
        if(!empty($servers)){
            $_cache_handler = new Cache_Memcache($servers);
            Yaf_Registry::set("cache_MemCache", $_cache_handler);
        }
        //从这边分开
        if (!empty($this->_config->cache->caching_system))
        {
            Yaf_Registry::set('cache_exclude_table', explode('|', $this->_config->cache->cache_exclude_table));
            Yaf_Loader::import(APPLICATION_PATH . '/library/Cache/Cache.php');
            if (isset($this->_config->cache->prefix))
            {
                define('CACHE_KEY_PREFIX', $this->_config->cache->prefix);
            }
            if (isset($this->_config->cache->object_cache_enable) && $this->_config->cache->object_cache_enable)
            {
                define('OBJECT_CACHE_ENABLE', true);
            }
            else
            {
                define('OBJECT_CACHE_ENABLE', false);
            }
        }
        else
        {
            define('MYSQL_CACHE_ENABLE', false);
            define('OBJECT_CACHE_ENABLE', false);
        }
    }


    public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
        $router = Yaf_Dispatcher::getInstance()->getRouter();  //通过派遣器得到默认的路由器
        /**
         * add the routes defined in ini config file 添加配置中的路由
         */
        $router->addConfig(Yaf_Registry::get("config")->routes);
	}

    public function _initSmarty(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import("Smarty/Adapter.php");
        $smarty = new Smarty_Adapter(null, Yaf_Registry::get("config")->get("smarty")->get("index"));
        Yaf_Registry::set("smarty", $smarty);
        $dispatcher->setView($smarty);
    }

    public function _initLayout(Yaf_Dispatcher $dispatcher){
        /*layout allows boilerplate HTML to live in /views/layout rather than every script*/
        $layout = new LayoutPlugin('layout/layout.html');
        /* Store a reference in the registry so values can be set later.
         * This is a hack to make up for the lack of a getPlugin
         * method in the dispatcher.
         */
        Yaf_Registry::set('layout', $layout);
        /*add the plugin to the dispatcher*/
        $dispatcher->registerPlugin($layout);
    }

}
