<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if ( !class_exists('Cache_Abstract', false))
{
    require dirname(__FILE__) . '/Abstract.php';
}

/**
 * 
 */
class Cache_Memcache extends Cache_Abstract
{
    private $_cacheCfg  = array();

    private $_conn  = null;

    public function __construct(Array $cfg)
    {
        if (!empty ($cfg['host']))   $this->_cacheCfg['host']   = $cfg['host'];
        if (!empty ($cfg['port']))   $this->_cacheCfg['port']   = $cfg['port'];
        if (!empty ($cfg['timeout']))   $this->_cacheCfg['timeout'] = $cfg['timeout'];
        if (isset ($cfg['pconnect']))   $this->_cacheCfg['pconnect']    = (bool) $cfg['pconnect'];

        return parent::__construct($this->_cacheCfg);
    }

    /**
     * 从缓存中提取一条数据
     *
     * @param string|array $token
     * @return mixed
     */
    public function load($token)
    {
        $link   = $this->_getLink();
        if (!empty($link))
        {
            $ret    =  memcache_get($link, $token);
            if ($ret === false)
            {
                return null;
            }
            return $ret;
        }
        return null;
    }

    /**
     * 检查缓存是否存在并有效
     *
     * @param   string|array $token
     * @return boolean
     */
    public function test($token)
    {
        $link   = $this->_getLink();
        if (!empty($link))
        {
            if (is_array($token))
            {
                $ret    = array();
                foreach ($token as $key)
                {
                    if (memcache_get ($link, $key) !== false)
                    {
                        $ret[$key]  = true;
                    } else {
                        $ret[$key]  = false;
                    }
                }
                return $ret;
            }
            return memcache_get($link, $key) !== false;
        }
        return false;
    }

    /**
     * 向缓存系统里放置一条数据
     *
     * @param string $token
     * @param mixed  $value
     * @param integer $life_time
     * @return boolean
     */
    public function put($token, $value, $life_time = -1)
    {
        $link   = $this->_getLink();
        if (!empty($link))
        {
            if($life_time < 0)
            {
                $expire_time  = $this->_life_time + $this->_current_time;
            } elseif ($life_time == 0) {
                $expire_time  = $this->_current_time + 100 * 20;
            } else {
                $expire_time  = $this->_current_time + $life_time;
            }
			

            return memcache_set($link, $token, $value, MEMCACHE_COMPRESSED, $expire_time);
        }
        return false;
    }

    /**
     * 从缓存系统中移除一条数据
     *
     * @param string $token
     * @return boolean
     */
    public function remove($token)
    {
        $link   = $this->_getLink();
        if (!empty($link))
        {
            return memcache_delete($link, $token);
        }
        return false;
    }

    /**
     * 清空所有的缓存数据
     * @return boolean
     */
    public function flush()
    {
        $link   = $this->_getLink();
        if (!empty($link))
        {
            return memcache_flush($link);
        }
        return false;
    }

    private function _getLink()
    {
        if (empty($this->_conn))
        {
            if ($this->_cacheCfg['pconnect'])
            {
                $this->_conn    = memcache_pconnect($this->_cacheCfg['host'], $this->_cacheCfg['port'], $this->_cacheCfg['timeout']);
            } else {
                $this->_conn    = memcache_connect($this->_cacheCfg['host'], $this->_cacheCfg['port'], $this->_cacheCfg['timeout']);
            }
        }
        if ($this->_conn)
        {
            memcache_set_compress_threshold($this->_conn, 1024, 0.6);
        }
        return $this->_conn;
    }
}
?>