<?php
/**
 * 获取在线预约
 */
class Gerbbs extends WechatResponse
{
    protected $msg;

    public function __construct($fromUserName, $toUserName, $content)
    {
        parent::__construct($fromUserName, $toUserName);
    }

    public function run()
    {
        $this->msg = '互动专区正在建设中，敬请期待哦.'."\n".'您可前往<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa4548828c476fec4&redirect_uri=http%3A%2F%2Fsbx.kwanson.com%2F&response_type=code&scope=snsapi_userinfo&state=2048#wechat_redirect">微商城</a>逛逛先哦！';
    }


    public function __toString()
    {
        $responseObj = null;

        $responseObj = new TextResponse($this->fromUserName, $this->toUserName, $this->msg);

        return $responseObj->__toString();
    }
}