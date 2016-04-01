<?php
/**
 * Created by PhpStorm.
 * User: Talent Gao
 * Date: 14-8-21
 * Time: 下午7:07
 */

require "swift_required.php";


class Swift_Adapter {

    private $_mailer;

    private $_transport;

    /**
     * Constructor
     *
     * @param array $cfg
     * @return void
     */
    public function __construct(Array $cfg) {
        $this->_transport = Swift_SmtpTransport::newInstance($cfg['service'],25)
            ->setUsername($cfg['username'])
            ->setPassword($cfg['password']);

        $this->_mailer = Swift_Mailer::newInstance($this->_transport);
    }

    /**
     * 简单的一个邮件发送  text/html
     * @param array $from
     * @param array $to
     * @param $subject
     * @param $body
     */

    public function sendMail(Array $from,Array $to,$subject,$body){

        $message = Swift_Message::newInstance();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body, 'text/html', 'utf-8');
        try{
            $this->_mailer->send($message);
            echo 'This is Success';
        }catch (Swift_ConnectionException $e){
            echo 'There was a problem communicating with SMTP: ' . $e->getMessage();
        }
    }
}