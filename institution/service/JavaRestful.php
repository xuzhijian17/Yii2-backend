<?php
namespace institution\service;

use Yii;
use institution\lib\InstCommFun;
/**
 * 请求机构端接口类
 */
class JavaRestful
{
    public $token ='';//机构端token字符串
    public $apiurl = '';//请求机构端接口地址
    public $wlog = FALSE;//是否记录日志
    public $params = [];//提交参数
    public $requestMode = 0;//请求方式 0:post 1:get
    private $errInfo = [];//初始化产生错误信息
    
    const SUCC_CODE = '111';//机构接口返回成功码
    /**
     * 构造函数
     * @param string $apino 配置文件定义接口编号
     * @param array $params 提交参数
     * @param int $requestmode 请求方式0:post 1:get
     * @param bool $w 是否记录日志
     */
    public function __construct($apino,$params,$requestmode,$wlog=FALSE)
    {
        if (empty(Yii::$app->params['japi'][$apino])){
            $this->errInfo = ['code'=>'-999','desc'=>'接口编号未定义'];//接口编号未定义
        }
        if (empty(Yii::$app->params['JavaServerHost']) || empty(Yii::$app->params['japi'][$apino]) || empty(Yii::$app->params['JavaServerToken']))
        {
            $this->errInfo = ['code'=>'-999','desc'=>'JavaServerHost/japi参数未定义'];
        }
        $this->requestMode = $requestmode;
        $this->apiurl = Yii::$app->params['JavaServerHost'].Yii::$app->params['japi'][$apino];
        if ($requestmode==1 && !empty($params) && is_array($params)){
            $this->apiurl .='/'.implode('/',$params);
        }
        $this->wlog = $wlog;
        $this->params = $params;
        $this->token = Yii::$app->params['JavaServerToken'];
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
        $rs = $this->curlRequest($this->apiurl, $this->params);
        if (empty($rs)){
            return ['code'=>'-1002','desc'=>'内部服务处理超时'];
        }else {
            return json_decode($rs,true);
        }
    }
    /**
     * curl 请求方法
     * @param string base 请求地址
     * @param array params 请求参数
     * @param int is_json 是否json格式
     * @return string json数据
     */
    public function curlRequest($base,$params)
    {
        $ch = curl_init();
        $headers = array(
            "Authorization:{$this->token}",
        );
        if($this->requestMode==0)
        {
            $post_string = json_encode($params);//json格式提交
            array_push($headers, 'Content-Type:application/json;charset=UTF-8;','Content-Length:'.strlen($post_string));
            $options = array(
                CURLOPT_URL => $base,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER=>$headers,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $post_string,
            );
        }elseif ($this->requestMode==1)
        {
            $options = array(
                CURLOPT_URL => $base,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER=>$headers,
                CURLINFO_HEADER_OUT=>true,
            );
        }else {
            return false;
        }
        curl_setopt_array($ch, $options);
        $rs = curl_exec($ch);
        if ($this->wlog){
            $error = ($rs===false)?curl_error($ch):'';
            $curlinfo = curl_getinfo($ch);
            $total_time = isset($curlinfo['total_time'])?round($curlinfo['total_time'],4):'--';
            $http_code = isset($curlinfo['http_code'])?$curlinfo['http_code']:'--';
            $log = "request:url=>{$base}; params=>".json_encode($params)." | response:{$rs} | time:{$total_time} | httpcode={$http_code}".' |error:'.$error;
            InstCommFun::wLog($log,0,'javaApi_'.date('Ymd').'.log');
        }
        curl_close($ch);
        return $rs;
    }
}