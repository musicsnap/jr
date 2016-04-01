<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/18
 * Time: 9:42
 */
class Hxbank
{
    //定义
    //请求银行的地址
    const POST_BANK_URL = 'https://183.63.131.123:5008/btb/process.do?';
    //签名地址
    const SGIN_URL = 'http://www.sumibuy.com:8080/Bank/Sign';
    //验证签名地址
    const CHECK_URL = '';
    //错误代码返回值对应表  这个玩意不知道会不会从银行哪返回回来
    private $error_cfg = array(

        '440000'=>'系统异常',
        '440001'=>'其他错误信息',
        '440002'=>'该企业证件已签约',
        '440003'=>'法人身份证信息错误',
        '440004'=>'经办人身份证信息错误',

        '440005'=>'证件号码格式错误',
        '440006'=>'手机号码格式错误',
        '440007'=>'手机动态密码不正确',
        '440008'=>'客户信息不存在',
        '440009'=>'流水号重复',

        '440010'=>'处于非交易时间',
        '440011'=>'经办人信息不符',
        '440012'=>'账户余额不足',
        '440013'=>'客户不存在',
        '440014'=>'账户不存在',

        '440015'=>'客户没签约',
        '440016'=>'其他错误，主要是不支持某些域上送的值',
        '440017'=>'短信发送失败',
        '440018'=>'动态密码不存在',
        '440019'=>'动态密码已过期',

        '440020'=>'身份证验证失败，请确认信息无误',
        '440021'=>'身份验证通讯出错，请稍后再试',
        '440022'=>'企业开立失败',
        '440023'=>'企业开账户失败',
        '440024'=>'企业签约失败',

        '440025'=>'客户没有绑定账户,请先向华兴银行账户注入资金',
        '440026'=>'该企业证件存在已受理的签约',
        '440027'=>'正在处理该企业证件的签约，不允许重复签约',
        '440031'=>'客户持有理财产品，不能修改绑定账户',
        '440032'=>'客户有未对付理财收益，不能修改绑定账户',

        '440033'=>'客户电子账户存在余额，不能修改绑定账户',
        '440034'=>'数据更新失败',
        '440035'=>'联系人身份证信息错误',
        '449001'=>'摘要验证失败',
        '449002'=>'验证签名失败',
    );
    //传入一个数组 然后转为请求的数据格式
    public static function array2SginData($array = array()){
        if(is_array($array) && sizeof($array)>0){
            $data = '<MESSAGE>';
            foreach($array as $key =>$value){
                $data .= "<".trim(strtoupper($key)).">".trim($value)."</".trim(strtoupper($key)).">";
            }
            return $data.'</MESSAGE>';
        }else{
            throw new Exception("传入的数据不是有效的数组");
        }
    }
    //把数据请求签名接口拿到签名后的信息
    public static function  sginData($data =''){
        if(empty($data)){
            throw new Exception("数据为空");
        }
        $post_data = array ("message" =>$data,);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::SGIN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// post数据
        curl_setopt($ch, CURLOPT_POST, 1);
// post的变量

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);



        return $output;
    }

    //把签名后的数据去请求银行并拿到返回值
    public static function postBank($data){


        // var_dump($data);
//        //转成UTF-8在发送
//        $encoding =    mb_detect_encoding($data);
//
//          var_dump($data);
//          var_dump($encoding);
//
//          if($encoding == 'UTF-8' && mb_check_encoding($data,'UTF-8')){
//        }else{
//
//            $data = iconv($data);
////
////              $encoding =    mb_detect_encoding($data);
////
////              var_dump($data);
////              var_dump($encoding);
//
//
//        }

        $encode = mb_detect_encoding( $data, array('ASCII','UTF-8','GB2312','GBK'));


        if ( $encode !='UTF-8' ){
            $data= iconv($encode,'UTF-8',$data);

            var_dump($data);
        }

        echo $encode = mb_detect_encoding( $data, array('ASCII','UTF-8','GB2312','GBK'));



        //这个位置还要编码
        $post_data = array ("RequestData" =>urlencode($data),);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::POST_BANK_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// post数据
        curl_setopt($ch, CURLOPT_POST, 1);

        //设置头
        curl_setopt($ch,CURLOPT_HTTPHEADER,$arr =array('Content-Type:application/xml; charset=UTF-8','Content-Length:'.mb_strlen($data)));


        var_dump($arr);

// post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }

    public static function submit($array){
        return self::postBank( self::sginData( self::array2SginData($array)));
    }


    public static function xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }



    /*
     *
     * 还有一个不确定的信息就是 他返回的东西 是错误的消息还是错误的状态码
     * */

    public static function check($array) {
        if(empty($array) || !is_array($array) || sizeof($array) <1){
            //返回错误
            throw new Exception("传入的信息有误");
        }
        //信息正确的话 开始请求
        $data =   HXbank::array2SginData($array);
        $data =   HXbank::sginData($data);
        $data= iconv("UTF-8",'GBK',$data);
        if(empty($data)){
            throw new Exception("服务器没有返回任何信息");
        }
        //有信息开始解析
        $xml = HXbank::xml_to_array($data);

        if(!empty($xml['RSPCD'])){
            $xml['MESSAGE'] =$xml;

        }
        if(empty($xml['MESSAGE'])){
            //没有返回消息的话就是这个位置的处理
            throw new Exception("签名的服务器返回错误");

        }else{
            $msg = $xml['MESSAGE'];

            if(!empty($msg['RSPCD'])&& $msg['RSPCD']!='000000'){
                //这个字段不存在的话就是失败
                throw new Exception($msg['RSPMSG']);

            }else{
                return $msg;
            }
        }

    }



    //理财赎回配置
//
//        $redeemTo = array(
//        '1'=>'赎回到电子帐号',
//        '2'=>'赎回到绑定账户',
//        );
//

    public static function getCheckArray($array=array(),$TRNCD =''){

        $arr = array(
            'VERSION'=>'V001',
            'MESSAGEID'=>date("Ymd").time(),
            'TRNCD'=>$TRNCD,
            'SRCSYSID'=>'SHSM',//'TFT',
            'SRCSEQNO'=>date("Ymd").time().'123',
            'SRCTRNDT'=>date("Ymd"),
            'SRCTRNTM'=>date("His"),
            'BGNDT'=>date("Ymd"),
            'ENDDT'=>date("Ymd")
        );
        return array_merge($arr,$array);
    }



    public static function getProductCode(){
        $arr = array(
            'BGNROWNO'=>'1',
            'RQROWNUM'=>'20',

        );
        $arr =   self::getCheckArray($arr,'ZTSA44Q02');
        $res = HXbank::check($arr);


        $res['ROW'] = self::array2Row($res['ROW']);


        return $res;

    }


    public static function array2Row($row){
        if(empty($row[0])){
            return array(0=>$row);

        }else{
            return $row;
        }
    }

    public static function get7DayRate($productCode = ''){
        //请求的数组
        $arr = array(
            'PRDCD'=>$productCode,
            'BGNROWNO'=>'1',
            'RQROWNUM'=>'20',
            'BGNDT'=>date('Ymd',strtotime('-8 day')),
            'ENDDT'=>date('Ymd',strtotime('-1 day')),
        );

        $arr =   self::getCheckArray($arr,'ZTSA44Q12');
        $res = HXbank::check($arr);
        $res['ROW'] = self::array2Row($res['ROW']);
        return $res;

    }



}








//测试用的  取得验证码
//
//if(1!=1){
//
//    //取得短信
//    $test = array(
//        'TRSTYPE'=>"0",
//        'MOBILE'=>'15021829400'//'15021829400',
//    );
//    $test = getCheckArray($test,'ZTSA44M02');
//    var_dump($test);
//
//    try{ $rs =HXbank::check($test);
//        var_dump($rs);echo '<hr/>';
//    }catch (Exception $e){
//        echo $e->getMessage();
//        exit();
//
//    }
//}


//客户注册
//if(1!=1){
//    //用户注册的
//    $test1 = array(
//        'version' =>'V001',
//        'otpseqno' => '2015082511152100998',//短信序列码
//        'otpno' => 'G49Y5L',//短信验证码
//        'idtype' => 'W' ,
//        'idno' =>'30167685-6' ,
//        'idduedt' => '20180519' ,
//        'custname' => '上海塑米信息科技有限公司',
//        'legalname' => '吴杭',
//        'legalidtype' =>'A' ,
//        'legalidno' => '321323199109101954',
//        'legalidduedt' =>'20191010' ,
//        'zipcd' => '200000' ,
//        'fax' =>  '02161657933',
//        'addr' => '上海市浦东新区牡丹路60号东辰大厦708',
//        'actorname' => '郑章杰' ,
//        'actoridtype' => 'A' ,
//        'actoridno' => '321323199109101954' ,
//        'actoridduent' =>  '20191010' ,
//        'contactname' => '郑章杰' ,
//        'mobile' => '15021829400',
//    );
//
//
//    $test = getCheckArray($test1,'ZTSA44M01');
//    var_dump($test);
//
//    try{ $rs =HXbank::check($test);
//        var_dump($rs);echo '<hr/>';
//    }catch (Exception $e){
//        echo $e->getMessage();
//        exit();
//
//    }
//}


//客户信息查询
//if(1!=1){
//    //
//    $test = array(
//        'CUSTNO'=>'5000001862',
//    );
//
//
//    $test = getCheckArray($test,'ZTSA44Q01');
//    var_dump($test);
//    try{ $rs =HXbank::check($test);
//        var_dump($rs);echo '<hr/>';
//    }catch (Exception $e){
//        echo $e->getMessage();
//        exit();
//
//    }
//
//
//
//}

//账户信息查询
//if(1 != 1){
//
//    $test = array(
//        'CUSTNO'=>'5000001862',
//        'OTPCHECKFLAG' => '0',//短信序列码
//    );
//
//
//    $test = getCheckArray($test,'ZTSA44Q09');
//    var_dump($test);
//
//    try{ $rs =HXbank::check($test);
//        var_dump($rs);echo '<hr/>';
//    }catch (Exception $e){
//        echo $e->getMessage();
//        exit();
//    }
//}
//签约交易结果查询
//if(1 != 1){
//    $test = array(
//        'VERSION'=>'V002',
//        'OLDSRCSEQNO' => '201508251440473500123',//交易流水号
//    );
//    $test = getCheckArray($test,'ZTSA44Q11');
//    var_dump($test);
//    try{ $rs =HXbank::check($test);
//        var_dump($rs);echo '<hr/>';
//    }catch (Exception $e){
//        echo $e->getMessage();
//        exit();
//    }
//}