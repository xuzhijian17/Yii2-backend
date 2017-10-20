<?php
namespace clientend\lib;

use clientend\lib\ClientCommFun;
use Yii;

/**
 * 请求B端接口类
 *
 */

class BServerRequest
{
    public $secretkey ='';//商户秘钥
    public $apiurl = '';//请求B端接口地址
    public $wlog = TRUE;//是否记录日志
    public $params = [];//提交参数
    private $errInfo = [];//初始化产生错误信息
    /**
     * 构造函数
     * @param string $apino 配置文件定义功能号
     * @param array $params 提交参数(参考汇成基金api文档)
     * @param bool $w 是否记录日志
     */
    public function __construct($apino,$params,$wlog=TRUE)
    {
        if (!isset($params['instid']) || empty($params)){
            $this->errInfo = ['code'=>'-3','message'=>'缺少商户id'];
        }
        $partner = ClientCommFun::getPartnerInfo($params['instid']);
        if(empty($partner['PassWord'])){
            $this->errInfo = ['code'=>'-12','message'=>'未配置商户秘钥'];
        }
        $this->secretkey = $partner['PassWord'];
        if (empty(Yii::$app->params['apino'][$apino])){
            $this->errInfo = ['code'=>'-999','message'=>'接口编号未定义'];//接口编号未定义
        }
        if (empty(Yii::$app->params['BServerHost']) || empty(Yii::$app->params['apino'][$apino]))
        {
            $this->errInfo = ['code'=>'-999','message'=>'BServerHost或apino未定义'];
        }
        $this->apiurl = Yii::$app->params['BServerHost'].Yii::$app->params['apino'][$apino];
        $this->wlog = $wlog;
        $this->params = $params;
    }
    /**
     * 发起请求
     * @param string $url 请求地址
     */
    public function apiRequest()
    {
        if (!empty($this->errInfo)){
            return $this->errInfo;
        }
        $this->params['signmsg'] = $this->GetSign($this->params);
        $rs = $this->curlPost($this->apiurl, $this->params);
        if (empty($rs)){
            return ['code'=>'-1002','message'=>'内部服务处理超时'];
        }else {
            return json_decode($rs,true);
        }
    }
    /**
     * curl post方法
     * @param string base 请求地址
     * @param array params 请求参数
     * @param int is_json 是否json格式
     * @return string json数据
     */
    public function curlPost($base,$params)
    {
        $post_string = empty($params)?'':http_build_query($params);
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $base,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_string,
        );
        curl_setopt_array($ch, $options);
        $rs = curl_exec($ch);
        $error = ($rs===false)?curl_error($ch):'';
        curl_close($ch);
        if ($this->wlog){
            Yii::info("request:url=>{$base}; params=>".json_encode($params)." | response:{$rs} | time:".round(microtime(true)-$t1,4).'|error:'.$error,__METHOD__);
        }
        return $rs;
    }
    /**
     * 生成签名字符串
     * @param array $params 提交参数
     */
    public function GetSign($params)
    {
        ksort($params);
        $tokenStr = $this->secretkey;
        foreach($params as $k=>$v)
        {
            if(!empty($v) || $v == '0')
            {
                $tokenStr .= $k.$v;
            }
        }
        $tokenStr .= $this->secretkey;
        return strtoupper(md5($tokenStr));
    }
}