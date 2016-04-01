<?php
/**
 * Created by PhpStorm.
 * User: Windows
 * Date: 2016/4/1
 * Time: 15:27
 */
define("APPLICATION_PATH", realpath(dirname(__FILE__) . '/../../../')); //指向public的上一级
require APPLICATION_PATH . '/scripts/crontab/crontab.php';
global $_RegionNameMean;
//这边测试下数据库查询操作,OK,测试配置加载成功
$db = new BaseModel();
$config = Yaf_Registry::get('config')->get('database.config')->toArray();
$db2 = new Db_DbMySQLi($config['host'],$config['username'],$config['password'],$config['dbname']);
$db3 = Db_Db::getInstance();
$user_info = $db->getDB()->fetchRow("select realname from users where id='155'");
var_dump($db2->getVersion());
$res = $db3->getValue("select realname from users where id='155'");
var_dump($db3->getVersion());
echo "start expired quote";
