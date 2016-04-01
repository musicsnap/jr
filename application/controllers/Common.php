<?php
/**
 * Created by PhpStorm.
 * User: Talent Gao
 * Date: 14-8-22
 * Time: 上午10:50
 */

class CommonController extends Controller{

    public function init(){}

    public function muneeAction(){

        // Define webroot
        define('WEBROOT', APPLICATION_PATH ."/public");
        // Include munee.phar
        Yaf_Loader::import("Munee/autoload.php");
        //Error
        ob_clean();
        // Echo out the response
        echo \Munee\Dispatcher::run(
            new \Munee\Request(array(
                    'css' => array(
                        'lessifyAllCss' => false
                    ),
                    'image' => array(
                        'checkReferrer' => false
                    )
                )
            ));

        die();
    }


}