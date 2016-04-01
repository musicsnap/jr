<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/9/23
 * Time: 15:53
 */
class Sms
{
    const POSTURL = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage?';

    const NOT_SUFFICIENT_FUNDS = -1;//余额不足导致发送失败

    const USER_ERR = -2;//短信网关帐号错误

    const PWD_ERR   =-3;//密码错误

    const ARG_ERR  = -4;//参数不对应

    const IS_NOT_PHONE = -5;//手机号码不正确

    const CAN_NOT_REPEAT_SEND  = -6;//不允许重复发送

    const OK = 1;//成功

    const WAIT = 1;//等待发送

    const NO = -9 ;//未知原因

    const UPDATE_ERR = -7;//更新状态失败

    const WRITE_ERR = -8;//写入数据库失败

    const HAVE_NO_ROWS  = -10;//没有要发送的数据
    const REPEATIME = 60;     //重复发送的间隔，秒。
    const FAILTIME = 1800;      //失效的时间，秒。
    const CHANNELID = 1;
    const NOWTIME = 0;

   //发送配置
    private static $config =array(
        'username'    =>'sdk_shsumi',// 'sdk_shumiyx',
        'password'   => '897799',//,m'749943',
        'suffix'      =>'【塑米城】'
    );

    public static  function send($mobile,$content){
      $data = array(
          "account" => self::$config['username'],
          "password" => self::$config['password'],
          "destmobile" => $mobile,
          "msgText" => $content.self::$config['suffix'],
          "sendDateTime" => "",
      );

      //开始发送
      return self::https_request(self::POSTURL,$data);
    }

    public static function DatabaseSend(Db_MySQLi $db_MySQLi, $senddata, $dataType ='Login'){

        if($dataType == 'Login' || $dataType=='Findpassword' || $dataType=='Register'){
            $arr = array(
                'channel_id'=> self::CHANNELID,
                'receiver' =>  $senddata['mobile'],
                'type'  =>$dataType,
                'content'=>  $senddata['content'],
                'code'   => $senddata['code'],
                'status' => self::WAIT,
                'worker_id'=> "1",
                'send_time'=> time()
            );

            $sms_id =  $db_MySQLi->fetchOne("select id from sendsms where  receiver ='{$senddata['mobile']}'AND type = '{$dataType}'");

            if($sms_id){
                $sms_idt = $db_MySQLi->update('sendsms',$arr," id = {$sms_id}");
            }else{
                $sms_idt = $db_MySQLi->insert('sendsms',$arr);
            }
            if(empty($sms_idt)){
                return self::WRITE_ERR;
            }
        }

        $data = array(
             "account" => self::$config['username'],
             "password" => self::$config['password'],
             "destmobile" => $senddata['mobile'],
             "msgText" => $senddata['content'].self::$config['suffix'],
             "sendDateTime" => "",
        );
        return self::https_request(self::POSTURL,$data);
    }

    private static function https_request($url,$data =NULL){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_BINARYTRANSFER,false);
        $post_data = http_build_query($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$post_data);
        curl_setopt($curl, CURLOPT_URL,$url);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}