<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/29
 * Time: 18:45
 */
//防止表单重复提交的
class PreventSubmitAgain {
    const NAME = 'PHP_PREVENT_SUBMIT_AGAIN';
    private $token ='';
    //生成一个唯一的key
    private  function makeToken(){
        $string =  time()."k".rand();
        $string = base64_encode($string);
        $string = md5($string);
        return $string;
    }
    public function getToken($name){
        $this->token = $this->makeToken();
        $_SESSION[$name] = $this->token ;
        return $this->token;

    }
    public function setTokenToCookie(){
        setcookie(self::NAME,$this->token );
    }
    public function setTokenToSession(){
        Yaf_Session::getInstance()->offsetSet(self::NAME,$this->token);
    }
    public function getTokenSession($name){
        $session = Yaf_Session::getInstance()->offsetGet($name);
        if(isset($session)){
            return $session;
        }else{
            return "NO_SESSION";
        }
    }

    public function getTokenCookie(){
        if(isset($_COOKIE[self::NAME])){
            return $_COOKIE[self::NAME];
        }else{
            return "NO_COOKIE";
        }
    }
    public  function is_submited($name){
        if(isset($_REQUEST[$name])){
            return  $_REQUEST[$name]!=$this->getTokenSession($name);
        }
        else{
            return true;
        }
    }
}