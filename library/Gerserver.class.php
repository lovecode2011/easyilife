<?php
/**
 * 获取客服
 */
class Gerserver extends WechatResponse
{
    protected $msg;

    public function __construct($fromUserName, $toUserName, $content)
    {
        parent::__construct($fromUserName, $toUserName);
    }

    public function run()
    {
        global $db;
        global $log;

        $get_kf_id = 'select `kf_id` from '.$db->table('member').' where `openid`=\''.$this->toUserName.'\'';
        $kf_id = $db->fetchOne($get_kf_id);

        $get_nickname = 'select `nickname` from '.$db->table('wx_kf').' where `id`='.$kf_id;
        $nickname = $db->fetchOne($get_nickname);

        $this->msg = '连线成功,客服'.$nickname.'为您服务。您好，请问有什么可以帮到您？';
        $log->record('run :'.$this->msg);
    }


    public function __toString()
    {
        $responseObj = null;

        $responseObj = new TextResponse($this->fromUserName, $this->toUserName, $this->msg);

        global $log;
        $log->record($responseObj->__toString());
        return $responseObj->__toString();
    }
}