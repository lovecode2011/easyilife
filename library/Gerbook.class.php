<?php
/**
 * 获取在线预约
 */
class Gerbook extends WechatResponse
{
    protected $msg;

    public function __construct($fromUserName, $toUserName, $content)
    {
        parent::__construct($fromUserName, $toUserName);
    }

    public function run()
    {
        $this->msg = '专属客服连线成功，直接留言即可与客服进行交流，或者点击链接查看<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa4548828c476fec4&redirect_uri=http%3A%2F%2Fsbx.kwanson.com%2Freservation.php&response_type=code&scope=snsapi_userinfo&state=2048#wechat_redirect">我的预约</a>';
    }


    public function __toString()
    {
        $responseObj = null;

        $responseObj = new TextResponse($this->fromUserName, $this->toUserName, $this->msg);

        return $responseObj->__toString();
    }
}