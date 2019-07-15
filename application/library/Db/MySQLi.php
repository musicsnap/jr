<?php

/**
 * 这是一个基于MySQLi实现的数据库类包装，其主要目的在于封装常用的操作以及错误诊断
 * @package     db
 */

if (!class_exists('Db_Expr', false)) {
    require dirname(__FILE__) . '/Expr.php';
}

/**
 * 基于MySQLi的数据库包装类
 *
 */
class Db_MySQLi {
    /**
     * 数据库连接句柄
     *
     * @var resource
     */
    private $_link;
    /**
     * 查询完后的结果句柄
     *
     * @var resource
     */
    private $_result;
    /**
     * 是否开启调试模式
     *
     * @var boolean
     */
    private $_isDebug = false;
    /**
     * 配置项
     *
     * @var array
     */
    private $_cfg = array();
    /**
     * 服务器配置是否已经开启魔法引用
     *
     * @var boolean
     */
    private $_isMagicQuotesOn;
    /**
     * 是否记录执行过的SQL
     *
     * @var boolean
     */
    private $_logSql = true;
    /**
     * 执行过的SQL
     *
     * @var array
     */
    private $_executedSqls = array();
    /**
     * 语句是否在事务中
     *
     * @var boolean
     */
    private $_isInTransaction = false;
    /**
     * 记录的最多执行的SQL语句条数
     *
     * @var int
     */
    private $_maxLogedSql = 1000;
    /**
     * 已经记录的SQL语句条数
     *
     * @var int
     */
    private $_logedSqlCount = 0;

    /**
     * Enter description here...
     *
     * @param array $cfg
     */
    public function __construct(Array $cfg) {

        $this->_isMagicQuotesOn = get_magic_quotes_gpc();
        $this->_cfg['pconnect'] = false;
        $this->_cfg['host'] = $cfg['host'];
        $this->_cfg['username'] = $cfg['username'];
        $this->_cfg['password'] = $cfg['password'];
        $this->_cfg['charset'] = $cfg['charset'];
        $this->_cfg['pconnect'] = $cfg['pconnect'];
        $this->_cfg['dbname'] = $cfg['dbname'];
        $this->_isDebug = (boolean) $cfg['debug'];
        $this->_logSql = (boolean) $cfg['logSql'];
        $this->_maxLogedSql = (int) $cfg['maxLogedSql'];

    }

    /**
     * 执行SQL语句，并提取一行
     *
     * @param string $sql
     * @param int $type
     * @return array
     */
    public function fetchRow($sql = '', $type = MYSQLI_ASSOC) {
        $needClean = false;
       // echo $sql;
        if ($sql) {
            $this->execute($sql);
            $needClean = true;
        }

        if (empty ($this->_result)) return array();

        $row = mysqli_fetch_array($this->_result, $type);
        $needClean && mysqli_free_result($this->_result);

        if (empty ($row)) return array();
        return $row;
    }

    /**
     * 执行SQL语句，并以关联数组的形式提取出所有结果
     *
     * @param string $sql
     * @param int $count
     * @param int $offset
     * @return array
     */
    public function fetchAll($sql = '', $count = -1, $offset = 0) {
        if ($sql) {
            if ($count > 0) {
                if ($offset > 0) {
                    $sql = "{$sql} LIMIT {$offset}, {$count}";
                } else {
                    $sql = "{$sql} LIMIT {$count}";
                }
            }
            //echo $sql;
            $this->execute($sql);
        }

        if (empty($this->_result))  return array();

        $data = array();
        while ($r =mysqli_fetch_assoc($this->_result)) {
            $data[] = $r;
        }
        mysqli_free_result($this->_result);
        return $data;
    }

    /**
     * 提取出一列的所有结果
     *
     * @param string $sql
     * @return array
     */
    public function fetchCol($sql = '') {
       // echo $sql;
        if ($sql) {
            $this->execute($sql);
        }

        if (empty($this->_result))  return array();

        $ret = array();
        while ($r =mysqli_fetch_row($this->_result)) {
            $ret[] = $r[0];
        }
        mysqli_free_result($this->_result);
        return $ret;
    }

    /**
     * 以Key Value 的形式提出两列的所有行
     *
     * @param string $sql
     * @return array
     */
    public function fetchPair($sql = '') {
       // echo $sql;
        if ($sql) {
            $this->execute($sql);
        }

        if (empty($this->_result))  return array();

        $ret = array();
        while ($r = mysqli_fetch_row($this->_result)) {
            $ret[$r[0]] = $r[1];
        }
        mysqli_free_result($this->_result);
        return $ret;
    }

    /**
     * 取出一个查询结果，常用于 fetchOne('SELECT COUNT(*) FROM tbl')
     *
     * @param string $sql
     * @return mixed
     */
    public function fetchOne($sql = '') {
        //echo $sql;
        if ($sql) {
            $this->execute($sql);
        }

        if (empty($this->_result))  return null;

        $r = mysqli_fetch_row($this->_result);
        mysqli_free_result($this->_result);
        if (empty($r))
            return null;
        return $r[0];
    }

    /**
     * 从数据库中删除指定记录
     *
     * @param string $tablename
     * @param string $where
     * @return int
     */
    public function delete($tablename, $where = '') {
        if (!empty($where) && strpos($tablename, '.') !== false)
        {
            //这里指定了库名
            $tablename	= str_replace('.', '`.`', $tablename);
        }
        if ($where)
            $sql = "DELETE FROM `{$tablename}` WHERE {$where}";
        elseif (stripos($tablename, 'delete') !== false)
            $sql = $tablename;
        elseif ($this->_isInTransaction)
            $sql = "DELETE FROM `{$tablename}`"; //事务中不允许执行DDL,否则会影响事务
        else
            $sql = "TRUNCATE TABLE `{$tablename}`";
        $this->execute($sql);
        $ret = mysqli_affected_rows($this->_get_link());
        return $ret;
    }

    /**
     * 开启事务
     *
     * @return Db_MySQLi
     */
    public function beginTransaction() {
        if (!$this->_isInTransaction) {
            $this->execute('START TRANSACTION');
            $this->_isInTransaction = true;
        } elseif ($this->_isDebug) {
            $this->_showError('已经开启事务，请不要重复开启');
        }
        return $this;
    }

    /**
     * 提交事务
     *
     * @return Db_MySQLi
     */
    public function commit() {
        if ($this->_isInTransaction) {
            $this->execute('COMMIT');
            $this->execute('SET autocommit = 1');
            $this->_isInTransaction = false;
        } elseif ($this->_isDebug) {
            $this->_showError('未开启事务，不能提交');
        }
        return $this;
    }

    /**
     * 撤销事务
     *
     * @return Db_MySQLi
     */
    public function rollBack() {
        if ($this->_isInTransaction) {
            $this->execute('ROLLBACK');
            $this->execute('SET autocommit = 1');
            $this->_isInTransaction = false;
        } elseif ($this->_isDebug) {
            $this->_showError('未开启事务，无法回滚');
        }
        return $this;
    }

    /**
     * 插入一条数据
     *
     * @param string $tablename
     * @param array $data
     * @return int
     */
    public function insert($tablename, Array $data = null) {

        if (!empty($data)) {

            if (strpos($tablename, '.') !== false)
            {
                //这里指定了库名
                $tablename	= str_replace('.', '`.`', $tablename);
            }

            $columns = implode('`, `', array_keys($data));
            $values = array_values($data);
            $value_str = array();
            foreach ($values as $v) {
                if (is_int($v) || $v instanceof Db_Expr) {
                    $value_str[] = $v;
                } else {
                    $value_str[] = '\'' . $this->quote($v) . '\'';
                }
            }
            $value_str = implode(', ', $value_str);
            $sql = "INSERT INTO `{$tablename}` (`{$columns}`) VALUES ({$value_str})";
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        $link = $this->_get_link();
        $ret = mysqli_insert_id($link);
        if (!$ret)
            $ret = mysqli_affected_rows($link);
        return $ret;
    }

    /**
     * 更新数据库中的记录
     *
     * @param string $tablename
     * @param array $data
     * @param string $where
     * @return int
     */
    public function update($tablename, Array $data = null, $where = '') {

        if (!empty($data)) {

            if (strpos($tablename, '.') !== false)
            {
                //这里指定了库名
                $tablename	= str_replace('.', '`.`', $tablename);
            }

            $sets = array();
            foreach ($data as $k => $v) {
                if (is_int($v) || $v instanceof Db_Expr) {
                    $sets[] = "`{$k}` = {$v}";
                } else {
                    $v = $this->quote($v);
                    $sets[] = "`{$k}` = '{$v}'";
                }
            }
            $sets = implode(',', $sets);
            if (!empty($where))
                $where = "WHERE {$where}";
            $sql = "UPDATE `{$tablename}` SET {$sets} {$where}";
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        if(false === $result)   return false;
        $ret = mysqli_affected_rows($this->_get_link());
        return $ret;
    }

    /**
         * 将一个字符串安全的转义，以避免SQL注入
         *
         * @param string $string
         * @return string
         */
    public function quote($string) {
        if ($this->_isMagicQuotesOn) {
            $string = stripslashes($string);
        }
        return mysqli_real_escape_string($this->_get_link(), $string);
    }

    /**
     * 执行一条SQL语句
     *
     * @param string $sql
     * @return resource
     */
    public function execute($sql) {
        $this->_result = mysqli_query($this->_get_link(),$sql);
        if (!$this->_result && $this->_isDebug)
            $this->_showError($sql);
        if ($this->_logSql && $this->_logedSqlCount < $this->_maxLogedSql) {
            ++$this->_logedSqlCount;
            $this->_executedSqls[] = $sql;
        }
        return $this->_result;
    }

    /**
     * 设置为调试模式
     *
     * @param boolean $isDebug
     * @return Db_MySQLi
     */
    public function setDebug($isDebug = true) {
        $this->_isDebug = $isDebug;
        return $this;
    }

    /**
     * 是否记录执行过的SQL语句
     *
     * @param boolean $useSqlLog
     * @return Db_MySQLi
     */
    public function logSql($useSqlLog = true) {
        $this->_logSql = $useSqlLog;
        return $this;
    }

    /**
     * 是否在事物中
     *
     * @return boolean
     */
    public function isInTransaction()
    {
        return $this->_isInTransaction;
    }

    /**
     * 析构函数，撤销未完成的事务
     *
     */
    public function __destruct() {
        if ($this->_isInTransaction) {
            $this->rollBack();
        }
    }

    public function close()
    {
        if (!$this->_cfg['pconnect'] && $this->_link)
        {
            mysqli_close($this->_link);
        }
    }

    /**
     * 获取当前的数据库连接
     *
     * @return resource
     */
    protected function _get_link() {
        if (empty($this->_link)) {
            if ($this->_cfg['pconnect'])
            {
                $old_level  =  error_reporting(0);

                $this->_link = mysqli_connect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);

                if (empty($this->_link))
                {
                    $errno = mysqli_errno($this->_link);

                    if ($errno == 2006)
                    {
                        $this->_link    = mysqli_connect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);
                    }
                }
                error_reporting($old_level);
            }
            else
                $this->_link = mysqli_connect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);
            if (!$this->_link && $this->_isDebug)
                $this->_showError('Connect Error');
            mysqli_select_db( $this->_link,$this->_cfg['dbname']);
            if (!empty($this->_cfg['charset'])) {
                if (function_exists('mysslm_set_charset')) {
                    if (!mysqli_set_charset($this->_link,$this->_cfg['charset']) && $this->_isDebug)
                        $this->_showError('Charset error');
                } else {
                    if (!mysqli_query($this->_link,"SET NAMES '{$this->_cfg['charset']}'") && $this->_isDebug)
                        $this->_showError('Charset error');
                }
            }
        }
        return $this->_link;
    }

    /**
     * 获取执行过的SQL语句
     *
     * @return array
     */
    public function getSqls()
    {
        return $this->_executedSqls;
    }

    /**
     * 打印错误信息
     *
     * @param string $sql
     */
    protected function _showError($sql) {
        $errno = 0;
        $error = 'MySQL Connect Error';
        $time = date('Y-m-d H:i:s', time());
        if (!empty($this->_link)) {
            $errno = mysqli_errno($this->_link);
            $error = mysqli_error($this->_link);
        }
        echo <<<EOT
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>数据库出错提示</title>
    </head>
<body>
<style>
.main{
	font-size:12px;
	width:100%;
}
</style>
<div class="main">
	<div style="font-weight:bold;">站点错误报告：</div>
	<div width="80%" style="text-align:left">
	<p>
　　当您来到这个页面的时候，代表着这里出现了一个严重的错误。<br />
　　请您尝试 <a href="">点击这里</a> 来刷新页面。或者 <a href="/">点击这里</a>
返回站点首页。如果问题还没有解决，请尝试 <a href="mailto: somebody@nobody.com">联系管理员</a> 来解决此问题。</p>
  <ul>
    <li>错误编号:[$error:$errno]</li>
    <li>出错时间:[$time]</li>
    <li>执行操作:[$sql]</li>
  </ul>
	</div>
</div>
<pre>
EOT;
        debug_print_backtrace();
        echo <<< EOF
</pre>
</body>
</html>
EOF;
        exit(1);
    }


}