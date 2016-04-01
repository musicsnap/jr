<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-11
 * Time: 上午10:47
 */

require_once "sms/nusoap.php";

class SendSms {

    /**
     * @var account
     */
    private  $_account = NULL;

    /**
     * @var password
     */
    private  $_password = NULL;

    /**
     * @msgText
     */
    private  $_msgText = NULL;

    /**
     * @var encoding
     */
    private  $_encoding = 'utf8';

    /**
     * @var 设置Web Service地址，要求设置为
     */
    private  $_SendUrl = "http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService";

    /**
     * @var 设置Web Service wsdl地址，要求设置为
     */
    private  $_SendUrlWsdl = "http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService?wsdl";

    /**
     * 指针
     * @var
     */
    private $_Sms;

    private $_debug;

    private $_suffix;

    /**
     * @param Db_MySQLi $db_handler
     * @param array $config
     * @param bool $debug
     */
    public function __construct(array $config, $debug = false) {
        $this->_Sms = new nusoap_client($this->_SendUrlWsdl,true);

        $this->_account  = $config['account'];
        $this->_password = $config['password'];
        $this->_msgText  = $config['msgText'];
        $this->_suffix   = $config['suffix'];

        $this->_Sms->soap_defencoding = $this->_encoding;
        $this->_Sms->decode_utf8      = false;
        $this->_Sms->xml_encoding     = $this->_encoding;
        $err = $this->_Sms->getError();

        $this->_debug = $debug;

        if ($err) {
            return '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        }

    }

    /**
     * 获取用户信息
     * @return string
     */
    public function getUserinfo(){
        $params = array(
            'account' => $this->_account,
            'password' => $this->_password,
        );
        $result = $this->_Sms->call('getUserInfo', $params, $this->_SendUrl);

        if($this->_Sms->fault) {
            return '<h2>Fault (This is expected)</h2><pre>'; print_r($result); echo '</pre>';
        }else{
            $err = $this->_Sms->getError();
            if ($err) {
                return '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                return $result;
            }
        }
    }



    public function Send($mobile,$content){
        $params = array(
            'account' => $this->_account,
            'password' => $this->_password,
            'destmobile' =>  $mobile,
            'msgText' => $content.$this->_msgText.$this->_suffix,
        );
        $result = $this->_Sms->call('sendBatchMessage', $params, $this->_SendUrl);

        if($this->_Sms->fault) {
            return '<h2>Fault (This is expected)</h2><pre>'; print_r($result); echo '</pre>';
        }else{
            $err = $this->_Sms->getError();
            if ($err) {
                return '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                return $result;
            }
        }
    }

}
