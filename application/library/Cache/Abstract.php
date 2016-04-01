<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @date 2010-11-21
 * @package cache
 */

/**
 * 缓存接口
 *
 * @author WPS2000
 */
abstract class Cache_Abstract
{
    const LCK_TOKEN = '__SYSTEM_IS_LOCKED__';
    
    /**
     * 缓存默认保留时间
     * @var integer
     */
    protected $_life_time   = 5;

    /**
     * 当前的时间戳
     * @var integer
     */
    protected $_current_time    = 0;

    /**
     *
     * @param array $cfg 
     */
    public function  __construct(Array $cfg = null) {
        if (!empty($cfg['current_time']))
        {
            $this->_current_time    = $cfg['current_time'];
        }  else {
            $this->_current_time    = time();
        }
    }

    /**
     *
     * @param integer $life_time 设置默认的超时时间
     * @return Cache_Abstract 
     */
    public function setLifeTime($life_time)
    {
        $this->_life_time   = $life_time;
        return $this;
    }

    /**
     * 从缓存系统中提取数据，其是 load 的别名
     *
     * @param string|array $token
     * @return mixed
     */
    public function get($token)
    {
        return $this->load($token);
    }

    /**
     * 从缓存系统中移除一条缓存数据，是 remove 的别名
     *
     * @param string $token
     * @return boolean
     */
    public function clear($token)
    {
        return $this->remove($token);
    }

    /**
     * 锁定一个缓存项目
     *
     * @param string $token
     * @return true
     */
    public function lock($token)
    {
        return $this->put($token, self::LCK_TOKEN);
    }

    /**
     * 判断一个 token 是否处于被锁定的状态
     *
     * @param string $token
     * @return boolean
     */
    public function isTokenLocked($token)
    {
        return $this->valueIsLock($this->load($token));
    }

    /**
     * 判断取出来的值是否是锁定标志位
     *
     * @param string $value
     * @return boolean
     */
    public function valueIsLock($value)
    {
        return $value === self::LCK_TOKEN;
    }

    /**
     * 从缓存中提取一条数据
     * 
     * @param string|array $token
     * @return mixed
     */
    public abstract function load($token);

    /**
     * 检查缓存是否存在并有效
     * 
     * @param   string|array $token
     * @return boolean
     */
    public abstract function test($token);

    /**
     * 向缓存系统里放置一条数据
     *
     * @param string $token
     * @param mixed  $value
     * @param integer $life_time
     * @return boolean
     */
    public abstract function put($token, $value, $life_time = -1);

    /**
     * 从缓存系统中移除一条数据
     *
     * @param string $token
     * @return boolean
     */
    public abstract function remove($token);

    /**
     * 清空所有的缓存数据
     * @return boolean
     */
    public abstract function flush();
}
?>