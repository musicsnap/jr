<?php
/**
 * Created by PhpStorm.
 * User: Windows
 * Date: 2016/4/1
 * Time: 15:24
 */

date_default_timezone_set("Asia/Shanghai");
mb_internal_encoding("UTF-8");
$app = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
require_once APPLICATION_PATH . '/conf/Config.inc.php';
require_once APPLICATION_PATH . '/conf/defines.inc.php';
$app->bootstrap();
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
