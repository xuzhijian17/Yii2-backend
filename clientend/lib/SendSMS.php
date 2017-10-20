<?php
namespace clientend\lib;

use Yii;
use common\lib\CommFun;
/**
 *公用方法类(继承使用common\lib\CommFun)
 */
class SendSMS
{
    public $userId = "hcsj";
    public $password = "hcsj";
    // 平台Base URL
    // 由平台提供 http://{IP}:{port}/{version}
    // $baseUrlString =
    // "http://123.57.48.46:28080/HIF12";
    public $baseUrl = "http://123.57.48.46:28080/HIF12";
    public $enterpriseUrl = "http://localhost/yuecheng/rest/simple";

    public $code_map = [
        0 => '成功',
        1 => '手机号码在黑名单',
        2 => '手机号码不在白名单',
        3 => '短信内容中包含黑名单关键字',
        4 => '手机号码未找到相应运营商配置',
        5 => '手机号码格式错误',
        7 => '下发队列满需客户重发',
        8 => '发送超速需客户重发',
        9 => '未知错误',
        10 => '短信内容超长',
        11 => '预付费客户余额不足',
        12 => '含有未报备关键字',
        13 => '下行消息携带号码超过限制（默认为商户1s最大带宽）'
            ];

    /**
     * 查询当前预付费用户余额
     */
    function QueryMoney()
    {
        $url = $this->baseUrl . "/queryamtf/" . $this->userId;
        $data = json_encode ( array () );
        $return_content = $this->http_post_data ( $url, $data );
        return $return_content;
    }


    /**
     * 发送短信
     * @param $mobile
     * @param $content
     * @return mixed
     */
    public function send($mobile, $content)
    {
        $url = $this->baseUrl . "/mt";
        $data = json_encode ( array (
            'Userid' => $this->userId,
            'Passwd' => $this->password,
            'Cli_Msg_Id' => uniqid (),
            'Mobile' => $mobile,
            'Content' => $content
        ) );
        $return = $this->http_post_data ( $url, $data );
        $return = json_decode($return);
        $code = $return['Rets'][0]['Rspcode'];
        return ['code'=> $code, 'message'=>$this->code_map[$code]];
    }
    /**
     * 发送post数据
     *
     * @param $url
     * @param $data_string
     */
    public function http_post_data($url, $data_string) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen ( $data_string )
        ) );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();
        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        \Yii::error("短信商请求返回结果：".$return_content);
        return $return_content;
    }

    /**
     * 上行URL验证
     */
    public function postMourlverify() {
        $url = $this->enterpriseUrl;
        $data = json_encode ( array (
            'Cmd' => 'Test'
        ) );
        $return_content = $this->http_post_data ( $url, $data );
        return $return_content;
    }

    /**
     * 上行短信推送
     */
    public function postSmsmopush($mobile, $content) {
        $url = $this->enterpriseUrl . "/smsmopush";
        $data = json_encode ( array (
            'Msg_Id' => uniqid (),
            'Dest_Id' => '106901110001',
            'Mobile' => $mobile,
            'Content' => $content
        ) );

        $return_content = $this->http_post_data ( $url, $data );
        return $return_content;
    }

    /**
     * 上行状态报告推送
     */
    public function postSmsrptpush($mobile) {
        $url = $this->enterpriseUrl . "/smsrptpush";
        $data = json_encode ( array (
            'Msg_Id' => uniqid (),
            'Dest_Id' => '106901110001',
            'Mobile' => $mobile,
            'Status' => 'DELIVRD'
        ) );

        $return_content = $this->http_post_data ( $url, $data );
        return $return_content;
    }

}