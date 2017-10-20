<?php
namespace clientend\services;

use Yii;
use clientend\lib\ClientCommFun;
use common\lib\HundSun;
use clientend\lib\BServerRequest;
/**
 * 交易相关类
 */
class TradeService
{
    public $db;    //数据库连接串初始化
    
    public $uid;    //操作者uid
    
    public $fundCode;   //基金代码
    
    public $merid;  //商户号
    
    /**
     * 交易类构造方法
     * @param int $uid 用户id 
     * @param string $fundCode 基金代码
     */
    function __construct($uid,$fundCode = '')
    {
        $this->db = Yii::$app->db;
        $this->fundCode = $fundCode;
        $this->uid = $uid;
        $session = Yii::$app->session;
        if (isset($session['user_login']['Instid'])){
            $instid = $session['user_login']['Instid'];
        }else {
            $u = $this->getUserInfo();
            $instid = $u['instid'];
        }
        $this->merid = $instid;
    }
    /**
     * 获取基金信息
     * @return mixed array成功/false无数据
     */
    public function getFundInfo()
    {
        $fundInfo = ClientCommFun::GetFundInfo($this->fundCode);
        if (empty($fundInfo)){
            return false;
        }
        $fundInfo['businflag'] = '';
        if (in_array($fundInfo['FundState'], [1,2])){
            $fundInfo['businflag'] = '020';
        }
        if (in_array($fundInfo['FundState'],[6,7,0,8])){
            $fundInfo['businflag'] = '022';
        }
        return array_change_key_case($fundInfo);
    }
    /**
     * 判断基金交易状态
     * @param int $type 0可买/1可卖
     * @return bool true可交易/false不可交易
     */
    public function getTradeStatus($type)
    {
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo))
        {
            return false;
        }else {
            if ($type==0){
                return in_array($fundInfo['fundstate'],[1,2,6,7,0,8])?true:false;//可买状态true:可买false:不可买
            }elseif ($type==1){
                return in_array($fundInfo['fundstate'],[1,2,7,0,8,5])?true:false;//可卖状态true:可卖false:不可卖
            }else {
                return false;
            }
        }
    }
    /**
     * 获取银行卡/交易账号信息
     * @param string $bankacco 银行卡号
     * @return mixed array ['bankname'=>'银行名称','bankacco'=>'银行卡号','tradeacco'=>'交易账号',
     * 'oncelimit'=>'单笔限额','daylimit'=>'单日限额','icno'=>'银行图标 ']/bool false 为空s
     * 
     */
    public function getBankInfo($bankacco=null)
    {
        $subSql = empty($bankacco)?'':" AND ub.BankAcco = '{$bankacco}' ";
        $sql = "SELECT bi.*,ub.BankAcco,ub.TradeAcco FROM `user_bank` ub LEFT JOIN bank_info bi ON ub.BankSerial=bi.BankSerial WHERE ub.Uid = {$this->uid} {$subSql}";
        $bankInfo = $this->db->createCommand($sql)->queryOne();
        return array_change_key_case($bankInfo);
    }
    /**
     * 查询费率
     * @param int $type 0:认购1:日常申购2:日常赎回3:定期定额申购
     * @param string $share A前端收费B后端收费
     * @param string $innercode 聚源内部代码
     * @return array [0=>['minchargerate'=>'费率值','chargerateunit'=>'费率单位6:%/7:元','divstandunit1'=>'划分标准单位',
     * 'stdivstand1'=>'划分标准起始','endivstand1'=>'划分标准截止','chargeratedes'=>'费率描述','divintervaldes'=>'区间描述',
     * 'chargeratetype'=>'费率类别'],1=>[]]
     */
    public function getRateInfo($type,$share,$innercode)
    {
        switch ($type)
        {
            case 0:
                $chargeratetype = ($share=='B')?10020:10010;
            break;
            case 1:
                $chargeratetype = ($share=='B')?11020:11010;
            break;
            case 2:
                $chargeratetype = 12000;
            break;
            case 3:
                $chargeratetype = ($share=='B')?22020:22010;
            break;
            default:
                $chargeratetype = 11010;
            break;
        }
        $db_juyuan = Yii::$app->db_juyuan;
        $sql = "SELECT * FROM MF_ChargeRateNew WHERE InnerCode = '{$innercode}' AND IfExecuted = 1 AND ClientType = 10";
        $resArr = $db_juyuan->createCommand($sql)->queryAll();
        if (!empty($resArr))
        {
            $result = [];
            foreach ($resArr as $value) 
            {
                $c_type = ($chargeratetype==11020 || $chargeratetype==11010) && $value['ChargeRateType']== 19000;
                if ($value['ChargeRateType'] == $chargeratetype || $c_type)
                {
                    $tmp['minchargerate'] = sprintf('%01.2f',$value['MinChargeRate']);
                    $tmp['chargerateunit'] = $value['ChargeRateUnit'];
                    $tmp['divstandunit1'] = $value['DivStandUnit1'];
                    $tmp['stdivstand1'] = $value['StDivStand1'];
                    $tmp['endivstand1'] = $value['EnDivStand1'];
                    $tmp['chargeratedes'] = $value['ChargeRateDes'];
                    $tmp['divintervaldes'] = $value['DivIntervalDes'];
                    $tmp['chargeratetype'] = $value['ChargeRateType'];
                    if ($c_type){
                        unset($result);
                        $result[] = $tmp;
                        break;
                    }else {
                        $result[] = $tmp;
                    }
                }
            }
            return $result;
        }else {
            return false;
        }
    }
    /**
     * 风险评估
     * riskflag=1异常 /01正常
     * ['code'=>'0','message'=>'','data'=>['riskflag'=>'风险标示','riskmsg'=>'风险提示',
     * 'fundriskStr'=>'基金风险类型','riskbilityStr'=>'个人风险类型']]
     */
    public function getRisk()
    {
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo)){
           return ClientCommFun::clientHandleCode(-404);//获取不到基金数据
        }
        $businflag = $fundInfo['fundstate']==1?'020':'022';
        $priv_P006 = ['businflag'=>$businflag,'fundcode'=>$this->fundCode,'sharetype'=>$fundInfo['sharetype']];
        $hs_obj = new HundSun($this->uid);
        $resP006 = $hs_obj->apiRequest('P006',$priv_P006);
        if ($resP006['code']==HundSun::SUCC_CODE)
        {
            return ['code'=>'0','message'=>'','data'=>['riskflag'=>$resP006['riskflag'],'riskmsg'=>$resP006['riskmsg'],
                'fundriskStr'=>$resP006['fundriskStr'],'riskbilityStr'=>$resP006['riskbilityStr']]
            ];
        }else {
            return ClientCommFun::clientHandleCode(['code'=>'-101','message'=>'获取风险测评失败']);
        }
    }
    /**
     * 购买页数据获取
     * @return ['code'=>0,'message'=>'','data'=>['fundcode'=>'基金代码','fundname'=>'基金名称','bankname'=>'银行名称','bankacco'=>'银行卡号',
     * 'oncelimit'=>'单笔限额','daylimit'=>'单日限额','icon'=>'银行图标 ','ratelist'=>[0=>['minchargerate'=>'费率值','chargerateunit'=>'费率单位6:%/7:元',
     * 'divstandunit1'=>'划分标准单位','stdivstand1'=>'划分标准起始','endivstand1'=>'划分标准截止','chargeratedes'=>'费率描述','divintervaldes'=>'区间描述',
     * 'chargeratetype'=>'费率类别'],1=>[]],'startrate'=>'起始费率值','startbuyline'=>'起购金额']]
     */
    public function purchasePageData()
    {
        $dataArr = [];
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo)){
            return ClientCommFun::clientHandleCode(-404);//获取不到基金数据
        }
        if (!$this->getTradeStatus(0)){
            return ClientCommFun::clientHandleCode(-405);//基金处于非交易状态
        }
        if ($this->merid !=1000)
        {
            $bankInfo = $this->getBankInfo();
            if (empty($bankInfo)){
                return ClientCommFun::clientHandleCode(-200);//用户银行卡不存在
            }
            $dataArr['bankname'] = $bankInfo['bankname'];
            $dataArr['bankacco'] = substr($bankInfo['bankacco'], -4);
            $dataArr['oncelimit'] = $bankInfo['oncelimit'];
            $dataArr['daylimit'] = $bankInfo['daylimit'];
            $dataArr['icon'] = 'http://'.$_SERVER['HTTP_HOST'].$bankInfo['icon'];
        }
        //费率信息获取
        $rateType = $fundInfo['fundstate'] ==1?0:1;
        $rateArr = $this->getRateInfo($rateType,$fundInfo['sharetype'],$fundInfo['innercode']);
        if (empty($rateArr)){
            $dataArr['startrate'] = 0;
            $dataArr['ratelist'] = ['minchargerate'=>'','endivstand1'=>'','chargeratedes'=>'','divintervaldes'=>''];
        }else {
            $dataArr['startrate'] = min(array_column($rateArr,'minchargerate'));
            $dataArr['ratelist'] = $rateArr;
        }
        $dataArr['fundcode']=$this->fundCode;
        $dataArr['fundname'] = $fundInfo['fundname'];
        $dataArr['sharetype'] = $fundInfo['sharetype'];
        if($fundInfo)
        $dataArr['startbuyline'] = $fundInfo['businflag']=='022'?$fundInfo['minpurchaseamount']:$fundInfo['minsubscribamount'];
        //风险评估信息(ajax)
        return ClientCommFun::clientHandleCode(['code'=>'0','message'=>'成功','data'=>$dataArr]);
    }
    /**
     * 购买基金
     * @param string $applysum 购买金额
     * @param string $tradepassword 交易密码
     * @return array ['code'=>'B端返回码','message'=>'B端返回信息']//结果同B端文档一样
     */
    public function doPurchase($applysum,$tradepassword)
    {
        $user = $this->getUserInfo();
        if (empty($user)){
            return ClientCommFun::clientHandleCode(-7);//获取不到个人信息
        }
        if ($tradepassword != $user['pass']){
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
        return $bsr_obj->apiRequest();
    }
    /**
     * 获取个人信息user/银行卡号
     * key 小写 pass交易密码解密后
     */
    public function getUserInfo()
    {
        $user = $this->db->createCommand("SELECT u.*,ub.BankAcco FROM `user` u LEFT JOIN user_bank ub ON u.id = ub.Uid WHERE u.id = {$this->uid}")->queryOne();
        if (!empty($user))
        {
            $user = array_change_key_case($user);
            $user['pass'] = ClientCommFun::AutoEncrypt($user['pass'],'D');
            return $user;
        }else {
            Yii::error('获取不到用户信息uid='.$this->uid,__METHOD__);
            return false;
        }
    }
    /**
     * 交易结果
     * @param string $orderno 订单号
     * @return array ['code'=>'返回码','message'=>'','data'=>['tradetype'=>'交易类型0:买入;1:卖出;2:撤单3:定投','tradetypestr'=>'交易类型描述','fundcode'=>'基金代码','fundname'=>'基金名称',
     * 'applyamount'=>'申请金额','bankname'=>'银行名称','bankacco'=>'银行卡号','icon'=>'银行图标 ','steptime'=>[0=>['day'=>'受理日期','on'=>'1'],1=>['day'=>'确认日期','on'=>'1'],
     * 1=>['day'=>'查看收益日期','on'=>'1:到达0:未到达']]],
     * ]
     */
    public function getTradeResult($orderno)
    {
        $sql = "SELECT * FROM trade_order_{$this->merid} WHERE OrderNo = '{$orderno}'";
        $res = $this->db->createCommand($sql)->queryOne();
        if (!empty($res))
        {
            $dataArr['tradetype'] = $res['TradeType'];
            switch ($res['TradeType'])
            {
                case 0:
                    $dataArr['tradetypestr'] = '买入';
                break;
                case 1:
                    $dataArr['tradetypestr'] = '卖出';
                break;
                case 2:
                    $dataArr['tradetypestr'] = '撤单';
                break;
                case 3:
                    $dataArr['tradetypestr'] = '定投';
                break;
                default:
                    $dataArr['tradetypestr'] = '买入';
            }
            $dataArr['applyamount'] = $res['ApplyAmount'];
            $bankInfo = $this->getBankInfo();
            if (empty($bankInfo)){
                return ClientCommFun::clientHandleCode(-200);//用户银行卡不存在
            }
            $dataArr['bankname'] = $bankInfo['bankname'];
            $dataArr['bankacco'] = substr($bankInfo['bankacco'], -4);
            $fundInfo = ClientCommFun::GetFundInfo($res['FundCode']);
            $dataArr['fundname'] = empty($fundInfo['FundName'])?'未知':$fundInfo['FundName'];
            $dataArr['fundcode'] = $res['FundCode'];
            //时间步骤
            $dayFmt = date('Y-m-d');
            $tradeDays = ClientCommFun::getTradeDays($res['ApplyTime']);
            $stepTime[0] = ['day'=>substr($res['ApplyTime'],5),'on'=>$dayFmt>=substr($res['ApplyTime'],0,10)?'1':'0'];//受理日期
            if (substr($res['ApplyTime'],11) >= '15:00:00')
            {
                $stepTime[1] = empty($tradeDays[1])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[1]),'on'=>$dayFmt>=$tradeDays[1]?'1':'0'];
                $stepTime[2] = empty($tradeDays[2])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[2]),'on'=>$dayFmt>=$tradeDays[2]?'1':'0'];
            }else {
                $stepTime[1] = empty($tradeDays[0])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[0]),'on'=>$dayFmt>=$tradeDays[0]?'1':'0'];
                $stepTime[2] = empty($tradeDays[1])?['day'=>'','on'=>'0']:['day'=>self::formatDataHandle($tradeDays[1]),'on'=>$dayFmt>=$tradeDays[1]?'1':'0'];
            }
            $dataArr['steptime'] = $stepTime;
            return ClientCommFun::clientHandleCode(['code'=>'0','message'=>'','data'=>$dataArr]);
        }else {
            return ClientCommFun::clientHandleCode(['code'=>'-101','message'=>'订单号不存在']);
        }
    }
    /**
     * 赎回页面数据
     * ['bankname'=>'银行名称','bankacco'=>'银行卡号','icon'=>'银行logo','ratelist'=>[0=>['minchargerate'=>'费率值','chargerateunit'=>'费率单位6:%/7:元',
     * 'divstandunit1'=>'划分标准单位','stdivstand1'=>'划分标准起始','endivstand1'=>'划分标准截止','chargeratedes'=>'费率描述','divintervaldes'=>'区间描述',
     * 'chargeratetype'=>'费率类别'],1=>[]],'usableshare'=>'当前可用','minholdshare'=>'最小持有','minredemeshare'=>'最低赎回','sellrate'=>'当前卖出费率',
     * 'fundcode'=>'基金代码','fundname'=>'基金名称','sharetype'=>'收费方式']
     */
    public function sellPageData()
    {
        $dataArr = [];
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo)){
            return ClientCommFun::clientHandleCode(-404);//获取不到基金数据
        }
        if (!$this->getTradeStatus(1)){
            return ClientCommFun::clientHandleCode(-405);//基金处于非交易状态
        }
        if ($this->merid != 1000)
        {
            $bankInfo = $this->getBankInfo();
            if (empty($bankInfo)){
                return ClientCommFun::clientHandleCode(-200);//用户银行卡不存在
            }
            $dataArr['bankname'] = $bankInfo['bankname'];
            $dataArr['bankacco'] = substr($bankInfo['bankacco'], -4);
            $dataArr['icon'] = 'http://'.$_SERVER['HTTP_HOST'].$bankInfo['icon'];
        }
        
        $positionRs = $this->db->createCommand("SELECT * FROM `fund_position_{$this->merid}` WHERE Uid = '{$this->uid}' AND FundCode ='{$this->fundCode}'")->queryOne();
        if (empty($positionRs)){
            return ClientCommFun::clientHandleCode(-410);//不存在持仓份额
        }
        $dataArr['usableshare'] = $positionRs['CurrentRemainShare']<$positionRs['FreezeSellShare']?0:$positionRs['CurrentRemainShare']-$positionRs['FreezeSellShare'];
        $dataArr['minholdshare'] = $fundInfo['minholdshare'];//最小持有
        $dataArr['minredemeshare'] = $fundInfo['minredemeshare'];//最低赎回
        //费率获取
        $rateInfo = $this->getRateInfo(2,$fundInfo['sharetype'],$fundInfo['innercode']);
        if (empty($rateInfo)){
            $dataArr['ratelist'] = ['minchargerate'=>'','endivstand1'=>'','chargeratedes'=>'','divintervaldes'=>''];
            $dataArr['sellrate'] = 0;
        }else {
            $dataArr['ratelist'] = $rateInfo;
            //持有天数
            $positime = date_create($positionRs['InitTime']);
            $nowtime = date_create(date('Y-m-d H:i:s'));
            $interval = date_diff($nowtime,$positime);
            $posidays = empty($interval->days)?0:$interval->days;
            $sellrate = 0;
            foreach ($rateInfo as $val)
            {
                if ($val['divstandunit1'] ==1){
                    $n=365;
                }elseif ($val['divstandunit1'] ==2){
                    $n=30;
                }elseif ($val['divstandunit1'] ==3){
                    $n=1;
                }else {
                    $n=1;
                }
                if ($val['stdivstand1']*$n < $posidays && $posidays <$val['endivstand1']*$n)
                {
                    $sellrate = $val['minchargerate'];
                }
            }
            $dataArr['sellrate'] = $sellrate;//当前卖出费率
        }
        $dataArr['fundcode'] = $this->fundCode;
        $dataArr['fundname'] = $fundInfo['fundname'];
        $dataArr['sharetype'] = $fundInfo['sharetype'];
        return ClientCommFun::clientHandleCode(['code'=>0,'message'=>'','data'=>$dataArr]);
    }
    /**
     * 赎回基金
     *@param string $applyshare 赎回份额
     * @param string $tradepassword 交易密码
     * @return array ['code'=>'B端返回码','message'=>'B端返回信息']//结果同B端文档一样
     */
    public function doSell($applyshare,$tradepassword)
    {
        $user = $this->getUserInfo();
        if (empty($user)){
            return ClientCommFun::clientHandleCode(-7);//获取不到个人信息
        }
        if ($tradepassword != $user['pass']){
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
        $bsr_obj = new BServerRequest(7, $param);
        $res = $bsr_obj->apiRequest();
        return $res;
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
}