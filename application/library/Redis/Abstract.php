<?php
/**
 * redis
 * User: Administrator
 * Date: 2015/11/25
 * Time: 9:35
 */

class Redis_Abstract{
    /**
     * 表名和键的分割符号
     */
    const DELIMITER = '-';

    /**
     * 连接的库
     *db0-db15共16个库，默认为0
     * @var int
     */
    protected $_db = 0;

    /**
     * 时间
     * @var int
     */
    protected $_timeout=100;

    /**
     * 前缀
     *
     * @var string
     */
    static $prefix = "";
    /**
     * @var
     */
    private $_config;
    /**
     * redis连接对象，未选择库的
     *
     * @var \Redis
     */
    static $redis;

    public function __construct(){
        //获取配置信息
        $this->_config = Yaf_Registry::get('config')->get('redis.database.params')->toArray();
        if(!$this->_config){
            throw new Exception('redis connect setting failed');
        }
        //这两个暂时不知怎么用
        if (empty($this->_config['slave'])) $this->_config['slave'] = $this->_config['master'];
        if (isset($this->_config['prefix'])) {
            self::$prefix = $this->_config['prefix'] ? $this->_config['prefix'] : substr(md5($_SERVER['HTTP_HOST']), 0, 6);
        }
        if ( !extension_loaded('redis') ) {
            throw new Exception('redis failed to load');
        }
    }

    /**
     * 获取redis连接
     *
     * @staticvar null $redis
     * @return Redis
     * @throws Exception
     */

    public function getRedis()
    {
        self::$redis = new Redis();
        self::$redis->connect($this->_config['host'], $this->_config['port']);
        self::$redis->select($this->_db);
        return self::$redis;
    }

    /**
     * 给key增加前缀
     *
     * @param string $key
     * @return string
     */
    private function _addPrefix($key) {
        if (self::$prefix) {
            return self::$prefix . self::DELIMITER . $key;
        }
        return $key;
    }

    /**
     * 删除key
     *
     * @param string $key
     * @return
     */
    public function del($key) {
        return $this->getRedis()->del($this->_addPrefix($key));
    }

    /**
     * 获取keys
     *
     * @param string $pattern
     * @reutnr array
     */
    public function keys($pattern) {
        return $this->getRedis()->keys($pattern);
    }

    /**
     * 增加缓存
     *
     * @param string $key
     * @param mix $value
     */
    public function set($key, $value) {
        return $this->getRedis()->set($this->_addPrefix($key), $value);
    }

    /**
     * 根据key值获取缓存数据
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->getRedis()->get($this->_addPrefix($key));
    }

    /**
     * redis自增1
     *
     * @param string $key
     * @return int
     */
    public function incr($key) {
        return $this->getRedis()->incr($this->_addPrefix($key));
    }

    /**
     * redis自减1
     *
     * @param string $key
     * @return int
     */
    public function decr($key) {
        return $this->getRedis()->decr($this->_addPrefix($key));
    }

    /**
     * redis自减1
     *
     * @param string $key
     * @return int
     */
    public function decrby($key, $decrement) {
        return $this->getRedis()->decrby($this->_addPrefix($key), $decrement);
    }

    /**
     * 增加列表内的元素
     *
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function lpush($key, $value) {
        return $this->getRedis()->lpush($this->_addPrefix($key), $value);
    }

    /**
     * 获取列表内的元素
     *
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return mix
     */
    public function lrange($key, $start, $stop) {
        return $this->getRedis()->lrange($this->_addPrefix($key), $start, $stop);
    }

    /**
     * 增加集合内的元素
     *
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function sadd($key, $value) {
        return $this->getRedis()->sadd($this->_addPrefix($key), $value);
    }

    /**
     * 列出集合内的元素
     *
     * @param int $key
     * @return mix
     */
    public function smembers($key) {
        return $this->getRedis()->smembers($this->_addPrefix($key));
    }

}