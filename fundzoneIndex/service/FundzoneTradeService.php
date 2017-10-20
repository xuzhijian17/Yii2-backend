<?php
namespace fundzone\service;

use Yii;
use clientend\services\TradeService;
use clientend\lib\BServerRequest;
use clientend\lib\ClientCommFun;
/**
 *交易相关业务处理，继承扩展父类
 */
class FundzoneTradeService extends TradeService
{
    /**
     * 企业购买页面
     *  @return ['code'=>0,'message'=>'','data'=>['fundcode'=>'基金代码','fundname'=>'基金名称','bankname'=>'银行名称','bankacco'=>'银行卡号',
     * 'ratelist'=>[0=>['minchargerate'=>'费率值','chargerateunit'=>'费率单位6:%/7:元',
     * 'divstandunit1'=>'划分标准单位','stdivstand1'=>'划分标准起始','endivstand1'=>'划分标准截止','chargeratedes'=>'费率描述','divintervaldes'=>'区间描述',
     * 'chargeratetype'=>'费率类别'],1=>[]],'startrate'=>'起始费率值','startbuyline'=>'起购金额']]
     */
    public function companyPurchasePageData()
    {
        $parentData = parent::purchasePageData();
        if ($parentData['code']==0)
        {
            $data = $parentData['data'];
            $res = $this->db->createCommand("SELECT * FROM `company_attach` WHERE Uid = {$this->uid}")->queryOne();
            $data['bankname'] = empty($res['BankName'])?'未知':$res['BankName'];
            $data['bankacco'] = empty($res['BankAcco'])?'未知':'*****'.substr($res['BankAcco'], -4);
            return ['code'=>0,'message'=>'','data'=>$data];
        }else{
            return $parentData;
        }
    }
    /**
     * 企业购买基金
     * @param string $applysum 购买金额
     * @param string $password 登陆密码(对企业做交易密码)
     * @return array ['code'=>'B端返回码','message'=>'B端返回信息']//结果同B端文档一样
     */
    public function companyDoPurchase($applysum,$password)
    {
        $user = $this->getUserInfo();
        if (empty($user)){
            return ClientCommFun::clientHandleCode(-7);//获取不到个人信息
        }
        if (md5($password) != $user['loginpass']){
            return ClientCommFun::clientHandleCode(-8);//密码错误
        }
        //组装接口参数
        $param['instid'] = $user['instid'];
        $param['hcid'] = $this->uid;
        $param['orderno'] = ClientCommFun::getOrderNo($this->uid);
        $param['bankacco'] = $user['bankacco'];
        $param['applysum'] = $applysum;
        $param['tradepassword'] = $user['pass'];
        $param['fundcode'] = $this->fundCode;
        $bsr_obj = new BServerRequest(5, $param);
        $res = $bsr_obj->apiRequest();
        return $res;
    }
    /**
     * 企业赎回页面
     * ['bankname'=>'银行名称','bankacco'=>'银行卡号','icon'=>'银行logo','ratelist'=>[0=>['minchargerate'=>'费率值','chargerateunit'=>'费率单位6:%/7:元',
     * 'divstandunit1'=>'划分标准单位','stdivstand1'=>'划分标准起始','endivstand1'=>'划分标准截止','chargeratedes'=>'费率描述','divintervaldes'=>'区间描述',
     * 'chargeratetype'=>'费率类别'],1=>[]],'usableshare'=>'当前可用','minholdshare'=>'最小持有','minredemeshare'=>'最低赎回','sellrate'=>'当前卖出费率',
     * 'fundcode'=>'基金代码','fundname'=>'基金名称','sharetype'=>'收费方式']
     */
    public function companySellPageData()
    {
        $parentData = parent::sellPageData();
        if ($parentData['code'] ==0)
        {
            $data = $parentData['data'];
            $res = $this->db->createCommand("SELECT * FROM `company_attach` WHERE Uid = {$this->uid}")->queryOne();
            $data['bankname'] = empty($res['BankName'])?'未知':$res['BankName'];
            $data['bankacco'] = empty($res['BankAcco'])?'未知':substr($res['BankAcco'], -4);
            return ['code'=>0,'message'=>'','data'=>$data];
        }else{
            return $parentData;
        }
    }
    /**
     * 企业赎回基金
     * @param string $applyshare 赎回份额
     * @param string $password 登陆密码(对企业做交易密码)
     * @param string $mintredeem 巨额赎回处理标志  	0:放弃超额部分;1:继续赎回
     * @return array ['code'=>'B端返回码','message'=>'B端返回信息']//结果同B端文档一样
     */
    public function companyDoSell($applyshare,$password,$mintredeem=1)
    {
        $user = $this->getUserInfo();
        if (empty($user)){
            return ClientCommFun::clientHandleCode(-7);//获取不到个人信息
        }
        if (md5($password) != $user['loginpass']){
            return ClientCommFun::clientHandleCode(-8);//密码错误
        }
        //组装接口参数
        $param['instid'] = $user['instid'];
        $param['hcid'] = $this->uid;
        $param['orderno'] = ClientCommFun::getOrderNo($this->uid);
        $param['bankacco'] = $user['bankacco'];
        $param['applyshare'] = $applyshare;
        $param['tradepassword'] = $user['pass'];
        $param['fundcode'] = $this->fundCode;
        $param['mintredeem'] = $mintredeem;
        $bsr_obj = new BServerRequest(7, $param);
        $res = $bsr_obj->apiRequest();
        return $res;
    }
    /**
     * 撤单
     * @param string $applyserial 申请编号
     */
    public function WithDraw($applyserial)
    {
        $user = $this->getUserInfo();
        if (empty($user)){
            return ClientCommFun::clientHandleCode(-7);//获取不到个人信息
        }
        //企业端的申请日期15:00后不可撤单
        if ($user['instid'] ==1000)
        {
            $tradeRs = $this->db->createCommand("SELECT * FROM `trade_order_1000` WHERE ApplySerial = '{$applyserial}'")->queryOne();
            if (empty($tradeRs)){
                return ['code'=>'-101','message'=>'原撤单编号不存在'];
            }
            if (time() > strtotime($tradeRs['TradeDay'].' 15:00:00'))
            {
                return ['code'=>'-101','message'=>'所属交易日15:00之后不可以撤单'];
            }
        }
        //组装接口参数
        $param['instid'] = $user['instid'];
        $param['hcid'] = $this->uid;
        $param['orderno'] = ClientCommFun::getOrderNo($this->uid);
        $param['tradepassword'] = $user['pass'];
        $param['applyserial'] = $applyserial;
        $bsr_obj = new BServerRequest(6, $param);
        $res = $bsr_obj->apiRequest();
        return $res;
    }
    /**
     * 产生token
     * @param string $type 0 买 1卖
     * @return bool 
     */
    public function getToken($type)
    {
        $key = $type==0?'BuyToken':'SellToken';
        $session = Yii::$app->session;
        if (!empty($session['user_login']))
        {
            $user_login = $session['user_login'];
            //token为空产生新的/返回
            if (empty($user_login[$key]))
            {
                $token = ClientCommFun::getOrderNo($this->uid);
                $user_login[$key] = $token;
                $session['user_login'] = $user_login;
                return $token;
            }else {
                return $user_login[$key];
            }
            
        }else {
            return false;
        }
    }
    /**
     * 判断token是否可用
     * @param string $type 0 买 1卖
     * @param string $token 表单传递的token值
     * @return bool
     */
    public function isTokenValid($type,$token)
    {
        $key = $type==0?'BuyToken':'SellToken';
        $session = Yii::$app->session;
        if (!empty($session['user_login'][$key]))
        {
            if($session['user_login'][$key]==$token)
            {
                $user_login = $session['user_login'];
                unset($user_login[$key]);
                $session['user_login'] = $user_login;
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /**
     * token session重新填写
     * * @param string $type 0 买 1卖
     * @param string $token 表单传递的token值
     */
    public function setToken($type,$token)
    {
        $key = $type==0?'BuyToken':'SellToken';
        $session = Yii::$app->session;
        if (!empty($session['user_login']))
        {
            $user_login = $session['user_login'];
            //token为空产生新的/返回
            if (empty($user_login[$key]))
            {
                $user_login[$key] = $token;
                $session['user_login'] = $user_login;
                return true;
            }
        }else {
            return false;
        }
    }
}