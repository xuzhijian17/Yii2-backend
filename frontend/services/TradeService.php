<?php
namespace frontend\services;

use Yii;
use common\lib\CommFun;
use common\lib\HundSun;
use frontend\models\TradeOrder;
use frontend\models\ChargeRate;

/**
 * 交易相关类(申购、赎回、撤单等基础方法)
 * 包含对业务端接口,返回统一格式['code'=>'返回码','msg'=>'信息提示','list'=>[列表数据],'data'=>{字典数据}]
 */
class TradeService
{
    public $redis;  //redis对象初始化
    
    public $uid;    //操作者uid
    
    public $fundCode;   //基金代码
    
    public $fundList = [];   //可买基金列表信息
    
    public $obj_hs;     //HundSun 类对象
    
    public $merid = 0; //商户号
    
    const PREFIX_FUND_INFO = 'fundinfo_';//缓存基金信息前缀fundinfo_fundcode
    
    const DETAILCAPITALMODE = '01'; //支付方式 一般固定为托收代扣
    
    const HS_SUCC_CODE = 'ETS-5BP0000';//恒生接口成功码
    
    /**
     * 交易类构造方法
     * @param int $uid 用户id B端业务逻辑不需要传uid时默认赋值一个可用
     * @param int $merid 商户号
     * @param string $fundCode 基金代码
     */
    function __construct($uid=0,$merid=0,$fundCode = '')
    {
        $this->redis = Yii::$app->redis;
        $this->uid = empty($uid)?2:$uid;
        if ($merid==1000){
            $pubParams = ['usertype'=>'o'];
        }else {
            $pubParams = null;
        }
        $this->obj_hs = new HundSun($uid,$pubParams);
        $this->merid = $merid;
        $this->fundCode = $fundCode;
     }
    /**
     * 获取支付方式
     * @param string $fundCode 基金代码
     * @return array 恒生T002[items]接口返回 [0=>['bankname'=>'银行名称','tradeacco'=>'交易账号','bankacco'=>'银行卡号',
     * 'comefrom'=>'交易来源','detailcapitalmode'=>[0=>['key'=>'00','value'=>'柜台支付']],'bankserial'=>'银行编号','capicalmode'=>'资金方式']]
     */
    public function getPayment()
    {
        $fundInfo = $this->getFundInfo();
        if (!empty($fundInfo))
        {
            $privT002 = ['businflag'=>$fundInfo['businflag'],'fundcode'=>$this->fundCode,'sharetype'=>$fundInfo['sharetype']];
            $resT002 = $this->obj_hs->apiRequest('T002',$privT002);
            return empty($resT002['items'][0])?null:$resT002['items'];
        }else {
            return null;
        }
    }
    /**
     * 获取基金信息
     * @param string $fundCode 基金代码
     * @return array ['fundcode'=>'基金代码','sharetype'=>'收费方式','fundstate'=>'基金状态',
     * 'fundname'=>'基金名称','risklevel'=>'风险等级',
     * 'businflag'=>'业务类型/020认购/022申购','minshare'=>'最小持有份额','minvalue'=>'最小值',
     * 'sndminvalue'=>'追加投资最小值']
     */
    public function getFundInfo()
    {
        $fundinfo = CommFun::GetFundInfo($this->fundCode);
        if (empty($fundinfo)){
            return false;
        }
        $retArr['fundcode'] = $this->fundCode;
        $retArr['sharetype'] = $fundinfo['ShareType'];
        $retArr['fundstate'] = $fundinfo['FundState'];
        $retArr['fundname'] = $fundinfo['FundName'];
        $retArr['fundname'] = $fundinfo['FundName'];
        $retArr['risklevel'] = $fundinfo['FundRiskLevel'];
        $retArr['minshare'] = $fundinfo['MinHoldShare'];
        $retArr['minvalue'] = $fundinfo['MinPurchaseAmount'];
        $retArr['sndminvalue'] = $fundinfo['MinAddPurchaseAmount'];
        return $retArr;
    }
    /**
     * 可买基金列表获取 key = fund_list
     * @return array ['基金代码1','基金代码2']
     */
    public function getFundList()
    {
        $fund_list = json_decode($this->redis->get('fund_list'),true);
        if(empty($fund_list))
        {
            $resT001 = $this->obj_hs->apiRequest('T001');
            if ($resT001['code'] == HundSun::SUCC_CODE && !empty($resT001['items']))
            {
                foreach ($resT001['items'] as $key=>$val)
                {
                    $fundArr[] = $val['fundcode'];
                }
                $this->redis->set('fund_list',json_encode($fundArr));
                $this->redis->expire('fund_list',86400);
                return $fundArr;
            }else {
                Yii::error('T001请求错误,返回'.json_encode($resT001),__METHOD__);
                return false;
            }
        }else {
               return $fund_list;
        }
    }
    /**
     * 定投基金列表获取
     * redis key = valuavgr_list
     * @return array ['基金代码1','基金代码2']
     */
    public function getValuavgrList()
    {
        //获取可购买基金数据缓存
        $valuavgr_list = json_decode($this->redis->get('valuavgr_list'),true);
        if(empty($valuavgr_list))
        {
            $resI012 = $this->obj_hs->apiRequest('I012');
            if ($resI012['code'] == HundSun::SUCC_CODE && !empty($resI012['returnlist']))
            {
                foreach ($resI012['returnlist'] as $key=>$val)
                {
                    $valuavgrArr[] = $val['fundcode'];
                }
                $this->redis->set('valuavgr_list',json_encode($valuavgrArr));
                $this->redis->expire('valuavgr_list',86400);
                return $valuavgrArr;
            }else {
                Yii::error('I012请求错误,返回'.json_encode($resI012),__METHOD__);
                return false;
            }
        }else {
            return $valuavgr_list;
        }
    }
    /**
     * 定投页面支付方式
     * @return ['bankacco'=>'银行卡编号','bankserial'=>'银行编号',bankname'=>'银行名称','netconsign'=>'开通网上交易','tradeacco'=>'交易账号'...]
     */
    public function getValuBankList()
    {
        $resI016 = $this->obj_hs->apiRequest('I016',['fundcode'=>$this->fundCode]);
        if ($resI016['code'] == HundSun::SUCC_CODE && !empty($resI016['returnlist']))
        {
            return $resI016['returnlist'];
        }else {
            return false;
        }
    }
    /**
     * 新增定投页面数据
     * @return ['fundname'=>'基金名称','feerate'=>'费率','discountrate'=>'折扣','minvalue'=>'起购金额','maxvalue'=>'最大金额','paylist'=>['bankname'=>'银行名称','bankacco'=>'银行卡号',
     * 'onceLimit'=>'单笔限额','dayLimit'=>'单日限额','icno'=>'银行图标 ','tradeacco'=>'交易账号'],'sharetype'=>'收费类别','riskflag'=>'风险标志','riskmsg'=>'风险提示信息']
     */
    public function valuavgrPageData()
    {
        $db_local = Yii::$app->db_local;
        $banklist = $this->getValuBankList();
        $paylist = [];
        if (!empty($banklist) && is_array($banklist))
        {
            foreach ($banklist as $val_bank)
            {
                $bankArr = ['bankacco'=>empty($val_bank['bankacco'])?'':$val_bank['bankacco']];
                $bankArr['tradeacco'] = empty($val_bank['tradeacco'])?'':$val_bank['tradeacco'];
                if (!empty($val_bank['bankserial']))
                {
                    $row = $db_local->createCommand("SELECT * FROM `bank_info` WHERE BankId = '{$val_bank['bankserial']}'")->queryOne();
                    if (!empty($row))
                    {
                        $bankArr['onceLimit'] = $row['OnceLimit'];
                        $bankArr['dayLimit'] = $row['DayLimit'];
                        $bankArr['icno'] = $row['Icon'];
                        $bankArr['bankname'] = $row['BankName'];
                    }
                }
                $paylist[] = $bankArr;
            }
            $payone = empty($paylist[0])?[]:$paylist[0];
            $dataArr['paylist'] = $payone;//暂定一个支付方式，今后多张银行卡再放开
            $fundInfo = $this->getFundInfo();
            $dataArr['feerate'] = empty($fundInfo['feerate'])?'0':$fundInfo['feerate'];
            $dataArr['discountrate'] = empty($fundInfo['discountrate'])?'0':$fundInfo['discountrate'];
            $risk = $this->getRisk();
            $dataArr['riskflag'] = empty($risk['riskflag'])?'0000':$risk['riskflag'];
            $dataArr['riskmsg'] = empty($risk['riskmsg'])?'':$risk['riskmsg'];
            $dataArr['fundname'] = empty($fundInfo['fundname'])?'':$fundInfo['fundname'];
            $dataArr['sharetype'] = empty($fundInfo['sharetype'])?'':$fundInfo['sharetype'];
            //查询是否持有该基金,持有取最小追加，非持有取最低起购
            $privS001 = ['querytype'=>'1','fundcode'=>$this->fundCode];
            $resS001 = $this->obj_hs->apiRequest('S001',$privS001);
            if ($resS001['code'] == HundSun::SUCC_CODE && !empty($resS001['returnlist']))
            {
                //持有该基金
                $dataArr['minvalue'] = empty($fundInfo['minvalue'])?'0':$fundInfo['minvalue'];
            }else {
                $dataArr['minvalue'] = empty($fundInfo['sndminvalue'])?'0':$fundInfo['sndminvalue'];
            }
            $dataArr['maxvalue'] = empty($fundInfo['maxvalue'])?'0':$fundInfo['maxvalue'];
            return $dataArr;
        }else {
            return false;
        }
    }
    /**
     * 定投协议新增
     * @param string $password 交易密码
     * @param string $applysum 申购金额
     * @param string $cycleunit 交易周期单位 "0":月,"1":周,"2":日
     * @param string $tradeacco 交易账号
     * @param string $bankacco 银行账号
     * @param string $jyrq 交易日期
     * @param string $zzrq 终止日期 99991231 无限期
     * @param string $scjyrq 首次交易月份
     * @param string $jyzq 交易周期
     * 
     */
    public function doValuavgr($password,$applysum,$cycleunit,$tradeacco,$bankacco,$jyrq,$zzrq='99991231',$scjyrq='000000',$jyzq='1')
    {
        $fundInfo = $this->getFundInfo();
        $privI006 = ['applysum'=>$applysum,'cycleunit'=>$cycleunit,'fundcode'=>$this->fundCode,'jyrq'=>$jyrq,'scjyrq'=>$scjyrq,
            'sharetype'=>empty($fundInfo['sharetype'])?'A':$fundInfo['sharetype'],'tradeacco'=>$tradeacco,'zzrq'=>$zzrq,'bankacco'=>$bankacco,'jyzq'=>$jyzq
        ];
        $resI006 = $this->obj_hs->apiRequest('I006',$privI006);
        return $resI006;
    }
    /**
     * 定投协议列表
     * @return array ['applysum'=>'申请金额','totalcfmmoney'=>'累计确认金额','jyrq'=>'扣款时间/交易日期','nextdate'=>'下次扣款',
     * 'state'=>'状态 ("A":启用,"P":暂停,"H":终止)','fundname'=>'基金名称','xyh'=>'协议号']
     */
    public function valuavgrList()
    {
        $resI005 = $this->obj_hs->apiRequest('I005');
        if ($resI005['code'] == HundSun::SUCC_CODE && !empty($resI005['returnlist']))
        {
            $rsArr = [];
            foreach ($resI005['returnlist'] as $val)
            {
                $tmp['applysum'] = $val['applysum'];
                $tmp['totalcfmmoney'] = $val['totalcfmmoney'];
                $tmp['jyrq'] = $val['jyrq'];
                $tmp['nextdate'] = $val['nextdate'];
                $tmp['state'] = $val['state'];
                $tmp['fundname'] = $val['fundname'];
                $tmp['xyh'] = $val['xyh'];
                $rsArr[] = $tmp;
            }
            unset($tmp);
            return $rsArr;
        }else {
            return false;
        }
    }
    /**
     * @param string $xyh 协议号
     * @return array ['applysum'=>'申请金额','totalcfmmoney'=>'累计确认金额','jyrq'=>'扣款时间/交易日期','nextdate'=>'下次扣款','signday'=>'签约日期','cycleunit'=>'周期单位',
     * 'jyzq'=>'交易周期','tradeacco'=>'交易账号','zzrq'=>'终止日期','state'=>'状态 ("A":启用,"P":暂停,"H":终止)','fundname'=>'基金名称','fundcode'=>'基金代码',
     * 'xyh'=>'协议号','totalsucctime'=>'累计成功次数','bankname'=>'银行名称','bankacco'=>'银行卡号',
     * 'tradelist'=>['applysum'=>'金额','applydate'=>'申请日期','confirmstat'=>'确认状态','confirmflag'=>'确认标示']]
     */
    public function valuavgrDetail($xyh)
    {
        $resI005 = $this->obj_hs->apiRequest('I005',['xyh'=>$xyh]);
        if ($resI005['code'] == HundSun::SUCC_CODE && !empty($resI005['returnlist'][0]))
        {
            $rsArr['applysum'] = $resI005['returnlist'][0]['applysum'];
            $rsArr['totalcfmmoney'] = $resI005['returnlist'][0]['totalcfmmoney'];
            $rsArr['jyrq'] = $resI005['returnlist'][0]['jyrq'];
            $rsArr['nextdate'] = $resI005['returnlist'][0]['nextdate'];
            $rsArr['state'] = $resI005['returnlist'][0]['state'];
            $rsArr['fundname'] = $resI005['returnlist'][0]['fundname'];
            $rsArr['fundcode'] = $resI005['returnlist'][0]['fundcode'];
            $rsArr['xyh'] = $resI005['returnlist'][0]['xyh'];
            $rsArr['totalsucctime'] = (int)$resI005['returnlist'][0]['totalsucctime'];
            preg_match("/(?:\[)(.*)(?:\])/i",$resI005['returnlist'][0]['bankname'], $simpname);
            $rsArr['bankname'] = empty($simpname[1])?'':$simpname[1];
            $rsArr['bankacco'] = substr($resI005['returnlist'][0]['bankacco'],-4);
            $rsArr['signday'] = substr($rsArr['xyh'], 0,8);
            $rsArr['cycleunit'] = $resI005['returnlist'][0]['cycleunit'];
            $rsArr['jyzq'] = $resI005['returnlist'][0]['jyzq'];
            $rsArr['tradeacco'] = $resI005['returnlist'][0]['tradeacco'];
            $rsArr['zzrq'] = $resI005['returnlist'][0]['zzrq'];
            /****定投记录列表***(今后改为查本地)*************/
            $resS003 = $this->obj_hs->apiRequest('S003',['xyh'=>$xyh,'applyrecordno'=>'50']);
            if ($resS003['code'] == HundSun::SUCC_CODE && !empty($resS003['returnlist']))
            {
                foreach ($resS003['returnlist'] as $value) {
                    $tmp['applysum'] = $value['applysum'];
                    $tmp['applydate'] = date('Y-m-d',strtotime($value['applydate']));
                    $tmp['confirmstat'] = $value['confirmstat'];
                    $tmp['confirmflag'] = $value['confirmflag'];
                    $arr[] = $tmp;
                }
                $rsArr['tradelist'] = $arr;
                unset($tmp,$arr);
            }else {
                $rsArr['tradelist'] = [];
            }
            return $rsArr;
        }else {
            return false;
        }
    }
    /**
     * 定投协议变更
     * @param array $param=['xyh'=>'协议号','state'=>'协议状态','zzrq'=>'终止日期','jyzq'=>'交易周期','jyrq'=>'交易日期','cycleunit'=>'周期单位',
     * 'applysum'=>'金额'] I007私有参数列表
     */
    public function valuavgrChange($param)
    {
        $resI007 = $this->obj_hs->apiRequest('I007',$param);
        return $resI007;
    }
    /**
     * 风险评估
     */
    public function getRisk()
    {
        $fundInfo = $this->getFundInfo($this->fundCode);
        if (!empty($fundInfo))
        {
            $priv_P006 = ['businflag'=>$fundInfo['businflag'],'fundcode'=>$this->fundCode,'sharetype'=>$fundInfo['sharetype']];
            $resP006 = $this->obj_hs->apiRequest('P006',$priv_P006);
            return $resP006;
        }else {
            return null;
        }
    }
    /**
     * 购买页数据获取
     * @return ['fundname'=>'基金名称','feerate'=>'原费率','discountrate'=>'折扣后','minvalue'=>'起购金额','maxvalue'=>'最大金额','paylist'=>['bankname'=>'银行名称','bankacco'=>'银行卡号',
     * 'onceLimit'=>'单笔限额','dayLimit'=>'单日限额','icno'=>'银行图标 ','tradeacco'=>'交易账号'],'riskflag'=>'风险标志','riskmsg'=>'风险提示信息']
     */
    public function purchasePageData()
    {
        $fundInfo = $this->getFundInfo();
        if (!empty($fundInfo) && ($fundInfo['fundstate']=='1' || $fundInfo['fundstate']=='0'))
        {
            $db_local = Yii::$app->db_local;
            $banklist = $this->getPayment();
            $paylist = [];
            if (!empty($banklist) && is_array($banklist))
            {
                foreach ($banklist as $val_bank)
                {
                    $bankArr = ['bankacco'=>empty($val_bank['bankacco'])?'':substr($val_bank['bankacco'], -4)];
                    $bankArr['tradeacco'] = empty($val_bank['tradeacco'])?'':$val_bank['tradeacco'];
                    if (!empty($val_bank['bankserial']))
                    {
                        $row = $db_local->createCommand("SELECT * FROM `bank_info` WHERE BankId = '{$val_bank['bankserial']}'")->queryOne();
                        if (!empty($row))
                        {
                            $bankArr['onceLimit'] = $row['OnceLimit'];
                            $bankArr['dayLimit'] = $row['DayLimit'];
                            $bankArr['icno'] = $row['Icon'];
                            $bankArr['bankname'] = $row['BankName'];
                        }
                    }
                    $paylist[] = $bankArr;
                }
            }
            $payone = empty($paylist[0])?[]:$paylist[0];
            //获取费率折扣(暂时按最小区间内10000显示费率折扣)
            $privS021 = ['fundcode'=>$this->fundCode,'businflag'=>empty($fundInfo['businflag'])?'':$fundInfo['businflag'],
                'sharetype'=>empty($fundInfo['sharetype'])?'A':$fundInfo['sharetype'],'requestbala'=>'10000'];
            $resS021 = $this->obj_hs->apiRequest('S021',$privS021);
            $afterDiscountrate = !isset($resS021['fees'][0]['feerate'])?'0':$resS021['fees'][0]['feerate'];//打折后费率
            $dataArr['discountrate'] = !isset($resS021['fees'][0]['discountrate'])?'0':$resS021['fees'][0]['discountrate'];//折扣
            $dataArr['feerate'] = (empty($afterDiscountrate) || empty($dataArr['discountrate']))?'0':$afterDiscountrate/$dataArr['discountrate'];
            $risk = $this->getRisk();
            $dataArr['riskflag'] = empty($risk['riskflag'])?'0000':$risk['riskflag'];
            $dataArr['riskmsg'] = empty($risk['riskmsg'])?'':$risk['riskmsg'];
            $dataArr['fundname'] = empty($fundInfo['fundname'])?'':$fundInfo['fundname'];
            $dataArr['paylist'] = $payone;//暂定一个支付方式，今后多张银行卡再放开
            //查询是否持有该基金,持有取最小追加，非持有取最低起购
            $privS001 = ['querytype'=>'1','fundcode'=>$this->fundCode];
            $resS001 = $this->obj_hs->apiRequest('S001',$privS001);
            if ($resS001['code'] == HundSun::SUCC_CODE && !empty($resS001['returnlist']))
            {
                //持有该基金
                $dataArr['minvalue'] = empty($fundInfo['minvalue'])?'0':$fundInfo['minvalue'];
            }else {
                $dataArr['minvalue'] = empty($fundInfo['sndminvalue'])?'0':$fundInfo['sndminvalue'];
            }
            $dataArr['maxvalue'] = empty($fundInfo['maxvalue'])?'0':$fundInfo['maxvalue'];
            return $dataArr;
        }else {
            return false;
        }
        
    }
    /**
     * 申购/认购 基金（恒生接口实现）
     * @param string $applysum 申请额度
     * @param string $password 交易密码
     * @param string $tradeacco 交易账号(由用户在上一个接口选择可支付方式获取)
     * @param string $orderno 订单编号
     * @return array ['code'=>'返回码','msg'=>'返回信息']
     */
    public function doPurchase($applysum,$password,$tradeacco,$orderno)
    {
        if (!$this->ckPassWord($password))
        {
            return ['code'=>'-1003','message'=>'密码错误'];
        }
        $fundInfo = $this->getFundInfo();
        $privT003 = ['applysum'=>$applysum,'businflag'=>empty($fundInfo['businflag'])?'022':$fundInfo['businflag'],'fundcode'=>$this->fundCode,
            'sharetype'=>empty($fundInfo['sharetype'])?'A':$fundInfo['sharetype'],'tradeacco'=>$tradeacco,'tradepassword'=>$password,
            'detailcapitalmode'=>self::DETAILCAPITALMODE];
        $resT003 = $this->obj_hs->apiRequest('T003',$privT003);
        if ($resT003['code'] == HundSun::SUCC_CODE)
        {
            $applyserial = empty($resT003['applyserial'])?'':$resT003['applyserial'];
            $this->orderHandle($orderno,$applyserial,0);
            return ['code'=>$resT003['code'],'msg'=>$resT003['message']];
        }else {
            return ['code'=>$resT003['code'],'msg'=>$resT003['message']];
        }
    }
    /**
     * 订单处理
     * @param string $orderno 订单编号
     * @param string $applyserial 申请编号
     * @param int $type 0:买入 1:卖出 2撤单
     * @param string $otherinfo 其他备注信息
     */
    public function orderHandle($orderno,$applyserial,$type,$otherinfo='')
    {
        $orderObj = new TradeOrder([],$this->merid);
        $orderRs = $orderObj->query("OrderNo = '{$orderno}' ",'one');
        if(!empty($orderRs))
        {
            if ($type ==0)
            {
                $orderUpObj = new TradeOrder(['ApplySerial'=>$applyserial,'TradeStatus'=>1,'ApplyTime'=>date('Y-m-d H:i:s')],$this->merid);
                return  $orderUpObj->update("OrderNo = '{$orderno}' ");
            }elseif ($type ==1)
            {
                return false;
            }elseif ($type ==2)
            {
                $orderUpObj = new TradeOrder(['TradeStatus'=>3,'OtherInfo'=>$otherinfo],$this->merid);
                return  $orderUpObj->update("OrderNo = '{$orderno}' ");
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /**
     * 基金撤单
     * @param string $orderno 订单号
     * @param string $tradepassword 交易密码
     * @return array ['code'=>'返回码','message'=>'返回信息']
     */
    public function doWithDraw($orderno,$tradepassword)
    {
        if (!$this->ckPassWord($tradepassword))
        {
            return ['code'=>'-1003','message'=>'密码错误'];
        }
        $orderObj = new TradeOrder([],$this->merid);
        $orderRs = $orderObj->query("OrderNo = '{$orderno}' ",'one');
        if (!empty($orderRs)) {
            $privT009 = ['applyserial'=>empty($orderRs['ApplySerial'])?'':$orderRs['ApplySerial'],'tradeacco'=>empty($orderRs['TradeAcco'])?'':$orderRs['TradeAcco'],'tradepassword'=>$tradepassword];
            $resT009 = $this->obj_hs->apiRequest('T009',$privT009);
            if ($resT009['code'] == HundSun::SUCC_CODE)
            {
                $infostr = "撤单时间:".date('Y-m-d H:i:s').";撤单编号:{$resT009['applyserial']}";
                $this->orderHandle($orderno,'',2,$infostr);
            }
            return $resT009;
        }else {
            return false;
        }
    }
    /**
     * 可撤单列表
     * @return array ['code'=>'返回码','message'=>'返回信息','list'=>[撤单列表],'data'=>[]]
     */
    public function withDrawList()
    {
        $resT008 = $this->obj_hs->apiRequest('T008');
        
        return ['code'=>$resT008['code'],'message'=>$resT008['message'],'list'=>empty($resT008['items'])?[]:$resT008['items'],'data'=>[]];
    }
    /**
     * 卖出页数据
     * @return array ['tradeacco'=>'交易账号','usableremainshare'=>'可用份额余额',,'fundname'=>'基金名称','minfree'=>'最小费率','maxfree'=>'最大费率',
     * 'fundcode'=>'基金代码',  'section'=>'卖出费率区间','minshare'=>'最低持有份额','sharetype'=>'收费方式','nextday'=>'下个交易日期']
     */
    public function sellPageData()
    {
        $privS001 = ['querytype'=>'1','fundcode'=>$this->fundCode];
        $resS001 = $this->obj_hs->apiRequest('S001',$privS001);
        if ($resS001['code'] == HundSun::SUCC_CODE && !empty($resS001['returnlist'][0]))
        {
            $dataArr['tradeacco'] = $resS001['returnlist'][0]['tradeacco'];
            $dataArr['usableremainshare'] = $resS001['returnlist'][0]['usableremainshare'];
            $dataArr['sharetype'] = $resS001['returnlist'][0]['sharetype'];
            $fundinfo = $this->getFundInfo();
            $dataArr['minshare'] = isset($fundinfo['minshare'])?'0':$fundinfo['minshare'];
            $dataArr['fundname'] = isset($fundinfo['fundname'])?'':$fundinfo['fundname'];
            $dataArr['fundcode'] = $this->fundCode;
            $dataArr['section'] = isset($fundinfo['sellrate'])?$fundinfo['sellrate']:'';
            if (!empty($dataArr['section']))
            {
                foreach ($dataArr['section'] as $val)
                {
                    $freeArr[] = $val['ChargeRateDesciption'];
                }
                $dataArr['minfree'] = min($freeArr);
                $dataArr['maxfree'] = max($freeArr);
            }else {
                $dataArr['minfree'] = $dataArr['maxfree'] = '0.00';
            }
            $dataArr['nextday'] = CommFun::getNextTradeDay(1);
            return $dataArr;
        }else {
            return false;
        }
    }
    /**
     * 赎回基金
     * @param string $applysum 申请额度
     * @param string $tradeacco 交易账号 (sellPageData中返回再传入)
     * @param string $sharetype 收费类别
     * @param string $password 交易密码
     * @param string $fundname 基金名称
     * @return array ['code'=>'返回码','message'=>'返回信息','data'=>'订单编号']
     */
    public function dosale($applysum,$tradeacco,$sharetype,$password,$fundname)
    {
        if (!$this->ckPassWord($password))
        {
            return ['code'=>'-1003','message'=>'密码错误'];
        }
        $privT006 = ['applysum'=>$applysum,'tradeacco'=>$tradeacco,'redeemuseflag'=>'1','saleway'=>'0',
            'customdelayflag'=>'0','fundcode'=>$this->fundCode,'sharetype'=>$sharetype];
        $resT006 = $this->obj_hs->apiRequest('T006',$privT006);
        if ($resT006['code'] == HundSun::SUCC_CODE) {
            $rs = $this->createOrder(0, 1, ['fname'=>$fundname], $tradeacco, $applysum,1,$resT006['applyserial']);
            return ['code'=>$resT006['code'],'message'=>$resT006['message'],'data'=>empty($rs)?'0':$rs];
        }else {
            return ['code'=>$resT006['code'],'message'=>$resT006['message'],'data'=>'0'];
        }
    }
    /**
     * 生成订单
     * @param string $applyAmount 申请金额 买入
     * @param int $tradeType 交易类型 0买入 1卖出
     * @param array $infoArr 订单信息['fname'=>'基金名称','bname'=>'银行名称','bacco'=>'银行尾号']
     * @param string $tradeacco 交易账号
     * @param string $applyShare 申请份额 卖出
     * @param int $tradeStatus 交易状态 0未付款 1未确认
     * @param string $applySerial 恒生交易账号(卖出)
     * @return mixed /false 失败 string 订单号
     */
    public function createOrder($applyAmount,$tradeType,$infoArr,$tradeacco,$applyShare,$tradeStatus=0,$applySerial='')
    {
        $orderNo = CommFun::getOrderNo($this->uid);
        $orderField = ['OrderNo'=>$orderNo,'Uid'=>$this->uid,'FundCode'=>$this->fundCode,'ApplyAmount'=>$applyAmount,'ApplyShare'=>$applyShare,'TradeStatus'=>$tradeStatus,
            'TradeType'=>$tradeType,'TradeAcco'=>$tradeacco,'InfoJson'=>empty($infoArr)?'':json_encode($infoArr,JSON_UNESCAPED_UNICODE),'SysTime'=>date('Y-m-d H:i:s'),
            'ApplyTime'=>($tradeType==1)?date('Y-m-d H:i:s'):null,'ApplySerial'=>$applySerial
        ];
        $orderObj = new TradeOrder($orderField,$this->merid);
        $rs = $orderObj->insert();
        if ($rs >0)
        {
            return $orderNo;
        }else {
            return false;
        }
    }
    /**
     * 订单详情
     * @param string $orderno 订单号
     * @return array ['tradeType'=>'订单类型0买入;1卖出;2定投;3分红','fname'=>'基金名称','bname'=>'银行名称','bacco'=>'银行尾号','applyAmount'=>'申请金额','applyShare'=>'申请份额',
     * 'orderNo'=>'订单号','status'=>'交易状态-1:已失效;0:未付款;1:未确认;2:已确认;3已撤单','stepTime'=>[0=>['day'=>'受理日期','on'=>'1'],1=>['day'=>'确认日期','on'=>'1'],
     * 1=>['day'=>'查看收益日期','on'=>'1:到达0:未到达']],'sysTime'=>'系统时间yyyy-mm-dd H:i:s','confirmShare'=>'确认份额','confirmAmount'=>'确认金额','confirmNetValue'
     * =>'确认净值','poundage'=>'手续费','confirmTime'=>'确认时间','fundCode'=>'基金代码']
     */
    public function orderDetail($orderno)
    {
        $orderObj = new TradeOrder();
        $sql = "OrderNo = '{$orderno}' ";
        $orderRs = $orderObj->query($sql,'one');
        if (!empty($orderRs))
        {
            if (!empty($orderRs['ApplyTime']))
            {
                $dayFmt = date('Y-m-d');
                $tradeDays = CommFun::getTradeDays($orderRs['ApplyTime']);
                $stepTime[0] = ['day'=>substr($orderRs['ApplyTime'],5),'on'=>$dayFmt>=substr($orderRs['ApplyTime'],0,10)?'1':'0'];//受理日期
                if (substr($orderRs['ApplyTime'],11) >= '15:00:00')
                {
                    $stepTime[1] = empty($tradeDays[1])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[1]),'on'=>$dayFmt>=$tradeDays[1]?'1':'0'];
                    $stepTime[2] = empty($tradeDays[2])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[2]),'on'=>$dayFmt>=$tradeDays[2]?'1':'0'];
                }else {
                    $stepTime[1] = empty($tradeDays[0])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[0]),'on'=>$dayFmt>=$tradeDays[0]?'1':'0'];
                    $stepTime[2] = empty($tradeDays[1])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[1]),'on'=>$dayFmt>=$tradeDays[1]?'1':'0'];
                }
            }else {
                $stepTime = [];
            }
            
            $info = json_decode($orderRs['InfoJson'],true);
            $data = ['tradeType'=>$orderRs['TradeType'],'fname'=>empty($info['fname'])?'':$info['fname'],'bname'=>empty($info['bname'])?'':$info['bname'],
                'bacco'=>empty($info['bacco'])?'':$info['bacco'],'applyAmount'=>$orderRs['ApplyAmount'],'applyShare'=>$orderRs['ApplyShare'],'orderNo'=>$orderno,
                'status'=>$orderRs['TradeStatus'],'stepTime'=>$stepTime,'sysTime'=>$orderRs['SysTime'],'confirmShare'=>$orderRs['ConfirmShare'],
                'confirmAmount'=>$orderRs['ConfirmAmount'],'confirmNetValue'=>$orderRs['ConfirmNetValue'],'poundage'=>$orderRs['Poundage'],'confirmTime'=>$orderRs['ConfirmTime'],
                'fundCode'=>$orderRs['FundCode']
            ];
            return $data;
        }else {
            return false;
        }
    }
    /**
     * 处理日期格式 
     * @param string $date 日期 yyyy-mm-dd
     * @return string  mm-dd 星期日 
     */
    private static function formatDataHandle($date)
    {
        $w = date('w',strtotime($date));
        $s = '';
        switch ($w)
        {
            case 0:
                $s = substr($date, 5).' 星期天';
                break;
            case 1:
                $s = substr($date, 5).' 星期一';
                break;
            case 2:
                $s = substr($date, 5).' 星期二';
                break;
            case 3:
                $s = substr($date, 5).' 星期三';
                break;
            case 4:
                $s = substr($date, 5).' 星期四';
                break;
            case 5:
                $s = substr($date, 5).' 星期五';
                break;
            case 6:
                $s = substr($date, 5).' 星期六';
                break;
            default:
                $s = '';
        }
        return $s;
    }
    /**
     * 交易确认标示转换
     */
    public static function confirmStaStr($d)
    {
        switch ($d)
        {
            case '4':
                $s = '已撤销交易 ';
                break;
            case '2':
                $s = '部分确认';
                break;
            case '5':
                $s = '行为确认';
                break;
            case '9':
                $s = '未处理';
                break;
            case '1':
                $s = '确认成功';
                break;
            case '3':
                $s = '实时确认成功';
                break;
            case '0':
                $s = '确认失败';
                break;
            default:
                $s = '';
        }
        return $s;
    }
    /**
     * 判断基金买入，卖出，定投按钮状态
     * @return array ['buy'=>'买入按钮 0隐藏1显示','sell'=>'卖出按钮 0隐藏1显示','invest'=>'定投按钮 0隐藏1显示']
     */
    public function fundButStatus()
    {
        $fundinfo = $this->getFundInfo();
        $arr['buy'] = isset($fundinfo['fundstate'])?($fundinfo['fundstate']=='1' || $fundinfo['fundstate']=='0')?'1':'0':'0';
        $privS001 = ['querytype'=>'1','fundcode'=>$this->fundCode];
        $resS001 = $this->obj_hs->apiRequest('S001',$privS001);
        if ($resS001['code'] == HundSun::SUCC_CODE && !empty($resS001['returnlist'][0]['usableremainshare']))
        {
            $arr['sell'] = '1';
        }else {
            $arr['sell'] = '0';
        }
        $valuav = $this->getValuavgrList();
        if (!empty($valuav) && in_array($this->fundCode, $valuav))
        {
            $arr['invest'] = '1';
        }else {
            $arr['invest'] = '0';
        }
        return $arr;
    }
    /**
     * 判断密码是否正确
     * @param string $pass 交易密码
     * @return bool true正确/false错误
     */
    public function ckPassWord($pass)
    {
        $session = Yii::$app->session;
        if (!empty($session['user_login']['Pass']))
        {
            if (CommFun::AutoEncrypt($session['user_login']['Pass'],'D') == $pass)
            {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
}