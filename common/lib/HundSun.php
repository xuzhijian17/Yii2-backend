<?php
namespace common\lib;

use Yii;
use common\lib\CommFun;
/**
 * 恒生接口相关方法实现
 */
class HundSun
{
    public $fundId = '';//恒生接口功能号
    
    public $pubParams = [];//恒生接口公共参数定义
    
    public $privateParams = [];//恒生接口私有参数
    
    public $pass = '';//恒生商户密码
    
    public $hostUrl = '';//恒生服务器地址 ip+port
    
    public $apiUrl = '';//恒生接口完整api地址
    
    public $uid;  //登录用户uid
    
    public $sessionkey = ''; //恒生接口sessionkey
    
    public $redis;  //redis对象初始化
    
    public $company = FALSE;//企业标志 true企业/false个人
    
    public $prefix_key = '';//缓存中sessionkey key前缀 拼接方式:sessionkey_uid
    
    const SUCC_CODE = 'ETS-5BP0000';//恒生接口成功码
    
    /**
     * 恒生接口调用构造函数
     * @param string $uid  用户id(本地)
     * @param string $pubParams 公共参数(非默认值)
     * @param string $host 恒生两台服务(0:8.2/1:8.3)
     */
    function __construct($uid=0,$pubParams=NULL,$host=0)
    {
        $hsConf = Yii::$app->params['hs_conf'];
        $this->pass = isset($hsConf['pass'])?$hsConf['pass']:'';
        if ($host==0){
            $this->hostUrl = isset($hsConf['host'])?$hsConf['host']:'';
            $this->prefix_key = 'sessionkey_';
        }elseif ($host==1){
            $this->hostUrl = isset($hsConf['host_cmd'])?$hsConf['host_cmd']:'';
            $this->prefix_key = 'sessionkey_cmd_';
        }else {
            $this->hostUrl = isset($hsConf['host'])?$hsConf['host']:'';
            $this->prefix_key = 'sessionkey_';
            Yii::error('host参数不正确=>'.$host,__METHOD__);
        }
        $this->pubParams = Yii::$app->params['pub_params'];
        $this->pubParams['timestamp'] = date('YmdHis');
        $this->uid = $uid;
        $this->redis = Yii::$app->redis;
        if (!empty($pubParams) && is_array($pubParams))
        {
            if ($pubParams['usertype'] =='o'){
                //企业参数
                $this->company = true;
                $this->pass = isset($hsConf['company_pass'])?$hsConf['company_pass']:'';
                $this->hostUrl = isset($hsConf['company_host'])?$hsConf['company_host']:'';
                $this->pubParams['merid'] = isset($hsConf['company_merid'])?$hsConf['company_merid']:'';
            }
            foreach ($pubParams as $key=>$val)
            {
                if (array_key_exists($key, $this->pubParams))
                {
                    $this->pubParams[$key] = $val;
                }else {
                    $this->pubParams[$key] = $val;
                }
            }
        }
    }
    
    /**
     * 请求恒生接口
     * @param string fundId 功能号
     * @param array params 私有参数列表
     * @param bool $w 是否写日志true记录;false不记录
     * @return array 返回信息(['code'=>'ETS-5BP0000','message'=>'','item1'=>'','item2'....])
     */
    public function apiRequest($fundId,$params=[],$w=TRUE)
    {
        $url = isset(Yii::$app->params['hs_relation'][$fundId]['url'])?Yii::$app->params['hs_relation'][$fundId]['url']:'';

		//无需sessionkey的接口
        $noNeedSession = ['P003','P005','C037','S010','S022','B040','B041','T001'];
        if (!in_array($fundId, $noNeedSession)) {
            $redis = $this->redis;
            $uid_key = $this->prefix_key.$this->uid;
            if (!empty($redis->get($uid_key))) {
                $this->sessionkey = $redis->get($uid_key);
            }else {
                $this->getSessionKey();
            }
        }

        $this->pubParams['sessionkey'] = $fundId=='P003'?'':$this->sessionkey;
        $this->pubParams['function'] = $fundId;
        $this->fundId = $fundId;
        $parArr = array_merge($this->pubParams,$params);
        $this->privateParams = $params;
        $signmsg = $this->doSort($parArr, $this->pass);
        $parArr['signmsg'] = md5($signmsg);
        $this->apiUrl = $this->hostUrl.$url;
        $result = $this->curlPost($this->apiUrl, $parArr,$w);
        return $this->doHandle($result,$fundId,$params);
    }
    
    /**
     * curl post方法
     * @param string base 请求地址
     * @param array params 请求参数
     * @param int is_json 是否json格式
     * @return string json数据
     */
    public function curlPost($base,$params,$w)
    {
        $post_string = empty($params)?'':http_build_query($params);
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $base,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_string,
        );
        curl_setopt_array($ch, $options);
        $rs = curl_exec($ch);
        $error = ($rs===false)?curl_error($ch):'';
        curl_close($ch);
        if ($w){
            Yii::info("request:url=>{$base}; params=>".json_encode($params)." | response:{$rs} | time:".round(microtime(true)-$t1,4).'|error:'.$error,'hundsun');
        }
        return $rs;
    }
    /**
     * 数组元素签名排序
     * @param array $param 排序数组
     * @param string $pkey 商户密码
     * @return string 拼接签名源字符串
     */
    public function doSort($param=[],$pkey='')
    {
        if(is_array($param) && !empty($param))
        {
            ksort($param);
            $tokenStr = $pkey;
            foreach($param as $k=>$v)
            {
                if(!empty($v) || $v == '0')
                {
                    $tokenStr .= $k.$v;
                }
            }
            $tokenStr .= $pkey;
            return $tokenStr;
        }else {
            return '';
        }
    }
    /**
     * 返回值结果处理
     * @param string $result 处理结果
     * @param string $fundId 功能号
     * @param array $privateParams 私有参数（调用apiRequest）
     * 处理内容:1.当sessionkey过期时重新获取
     */
    public function doHandle($result,$fundId,$privateParams)
    {
        if (!empty($result))
        {
            //当返回码 ETS-5BP9951，重新登录获取,存入缓存
            $rsArr = json_decode($result,true);
            if (isset($rsArr['results']['code']) && ($rsArr['results']['code']=='ETS-5BP9951' || $rsArr['results']['code']=='ETS-2B300'))
            {
                if ($this->getSessionKey())
                {
                    //重新调用接口
                    return $this->apiRequest($fundId,$privateParams);
                }
            }//else 其他情况处理追加
            else {
                return isset($rsArr['results'])?$rsArr['results']:['code'=>'-1002','message'=>'数据异常','custom'=>1];
            }
        }else {
            return ['code'=>'-1001','message'=>'内部服务处理超时','custom'=>1];
        }
    }
    /**
     * 获取用户信息
     * @return array 用户表字段信息，解密后明文
     */
    public function getUserInfo()
    {
        $uid = empty($this->uid)?0:$this->uid;
        $db_local = Yii::$app->db_local;
        $row = $db_local->createCommand("SELECT * FROM `user` WHERE id = '{$uid}'")->queryOne();
        if (isset($row['Pass']))
        {
            $row['Pass'] = CommFun::AutoEncrypt($row['Pass'],'D');
        }
        return $row;
    }
    /**
     * sessionkey 过期重取
     * @param string json 输入恒生返回json字符串
     * @return null
     */
    public function getSessionKey()
    {
        $uInfo = $this->getUserInfo();
        if (!empty($uInfo))
        {
            if ($this->company){
                //企业
                $db_local = Yii::$app->db_local;
                $tradeAcco = Yii::$app->db_local->createCommand("SELECT TradeAcco FROM `user_bank` WHERE Uid = '{$uInfo['id']}'")->queryScalar();
                $privArr = ['password'=>$uInfo['Pass'],'lognumber'=>$tradeAcco,'logtype'=>'0'];
            }else {
                //个人
                $privArr = ['certificatetype'=>'0','password'=>$uInfo['Pass'],'lognumber'=>$uInfo['CardID'],'logtype'=>'2'];
            }
            $p003RsArr = $this->apiRequest('P003',$privArr);//获取sessionkey
            if (isset($p003RsArr['code']) && $p003RsArr['code'] == self::SUCC_CODE)
            {
                $this->sessionkey = $p003RsArr['sessionkey'];
                $this->redis->set($this->prefix_key.$this->uid,$p003RsArr['sessionkey']);
                $this->redis->expire($this->prefix_key.$this->uid,1200);
                return true;
            }else {
                //日志记录，登陆接口调用失败
                Yii::error('sessionkey获取失败:respond'.json_encode($p003RsArr).'request:'.json_encode($privArr),__METHOD__);
                return false;
            }
        }else {
            Yii::error('获取不到用户记录uid='.$this->uid,__METHOD__);
            return false;
        }
    }
    /**
     * 登录恒生系统 (1.判断2.如果过期重新登录)
     */
    public function loginHs()
    {
        $uid_key = $this->prefix_key.$this->uid;
        if (empty($this->redis->get($uid_key))) {
            $this->getSessionKey();
        }
    }
}