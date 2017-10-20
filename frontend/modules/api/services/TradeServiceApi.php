<?php
namespace frontend\modules\api\services;

use frontend\modules\api\models\BankInfo;
use frontend\modules\api\models\UserBank;
use Yii;
use common\lib\CommFun;
use frontend\models\TradeOrder;
use frontend\services\TradeService;
use frontend\models\ValuTradePlan;
use common\models\FundPosition;
use common\models\TaskDeduct;
use Exception;

/**
 *交易相关类(申购、赎回、撤单等业务实现)
 */
class TradeServiceApi extends TradeService
{
    public $noNeedPass = false; //交易密码非必需用户
    /**
     * 构造方法
     * @param int $uid 用户id
     * @param int $merid 商户号
     * @param string $fundCode 基金代码
     */
    function __construct($uid,$merid,$fundCode = '')
    {
        parent::__construct($uid, $merid,$fundCode);
        if (CommFun::ckNoPass($merid))
        {
            $this->noNeedPass = true;
        }
    }
    /**
     * 申购(认购)处理函数
     * busintype 业务大类 现金宝取现：01；超级现金宝取现：02
     *
     * @param array $param=['orderno'=>'订单号','bankacco'=>'银行卡号','applysum'=>'申请金额','tradepassword'=>'交易密码']
     * @return array ['hd_res'=>'恒生结果','idemp_arr'=>'订单数据']处理结果
     */
    public function handlePurchase($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>2];
        $uinfo = $this->checkHandle($param,$idemp_arr,$param['bankacco']);
        //组装接口参数
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo))
        {
            CommFun::handleCode('-404',$idemp_arr);//获取不到基金数据
        }
        if (!in_array($fundInfo['FundState'],[1,2,6,7,0,8]))
        {
            CommFun::handleCode('-405',$idemp_arr);//基金处于非交易状态
        }
        if (empty($fundInfo['businflag']))
        {
            CommFun::handleCode('-402',$idemp_arr);//获取不到申购/认购状态
        }else {
            $fundInfo['businflag']=='020' && $fundInfo['MinSubscribAmount']<=$param['applysum'] ||
            $fundInfo['businflag']=='022' && $fundInfo['MinPurchaseAmount']<=$param['applysum'] or CommFun::handleCode('-417',$idemp_arr);
        }
        $user_bank = UserBank::getUserBankByBankAcco($param['bankacco']);
        if (empty($user_bank) || $user_bank['Status'] == -1) {
            CommFun::handleCode('-200', $idemp_arr);    //银行卡不存在
        }
        if ($this->merid !=1000)//企业用户不走易宝支付
        {
            $bankInfo = BankInfo::getBankQuotaInfo($user_bank['BankSerial']);
            if (empty($bankInfo)) { //银行不存在
                CommFun::handleCode('-205', $idemp_arr);
            }
            if ($bankInfo['status'] == 0) { //该银行未上线
                CommFun::handleCode('-206', $idemp_arr);
            }
        }
        $privT003 = [
            'applysum'  =>  $param['applysum'],
            'businflag' =>  $fundInfo['businflag'],
            'fundcode'  =>  $this->fundCode,
            'sharetype' =>  $fundInfo['ShareType'],
            'tradeacco' =>  $uinfo['tradeacco'],
            'tradepassword' =>  $uinfo['password'],
            'detailcapitalmode' => $this->merid==1000?'04':parent::DETAILCAPITALMODE
        ];
        //T+0产品标示
        $moneyFund = isset($fundInfo['MoneyFund'])?$fundInfo['MoneyFund']:0;
        $resT003 = $this->obj_hs->apiRequest('T003',$privT003);
        //在本地库记录
        if ($resT003['code'] == parent::HS_SUCC_CODE)
        {
            $rs = $this->orderHandleApi($param['orderno'],$resT003['applyserial'],0,$uinfo['tradeacco'],$param['applysum'],0,'',$moneyFund);
            if ($this->merid !=1000){
                //查询扣款时间
                $task = ['Oid'=>(int)$rs,'Instid'=>$this->merid,'Uid'=>$this->uid,'ApplySerial'=>$resT003['applyserial'],'TaskTime'=>date('Y-m-d H:i:s')];
                TaskDeduct::insert($task);
            }
        }else {
            $rs = 0;
            $errlog = "购买失败，接口返回:".var_export($resT003,true).'-提交参数:'.var_export($idemp_arr,true);
            Yii::error($errlog,__METHOD__);
        }
        unset($resT003['bankurl'], $resT003['capitalmode'], $resT003['form']);
        $idemp_arr['Oid'] = (int)$rs;
        return ['hd_res'=>$resT003,'idemp_arr'=>$idemp_arr];
    }
    /**
     * 撤单处理函数
     * @param array $param=['orderno'=>'订单号','applyserial'=>'申请编号','tradepassword'=>'交易密码']
     * @return array ['hd_res'=>'恒生结果','idemp_arr'=>'订单数据']处理结果
     */
    public function handleWithDraw($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>4];
        $uinfo = $this->checkHandle($param,$idemp_arr);
        //查询需撤单申请
        $orderObj = new TradeOrder([],$this->merid);
        $rs = $orderObj->query("ApplySerial = '{$param['applyserial']}' ",'one');
        if (empty($rs))
        {
            CommFun::handleCode('-407',$idemp_arr);
        }else {
            if ($rs['TradeStatus'] ==4){
                CommFun::handleCode('-413',$idemp_arr);//已撤单
            }
            if ($this->merid !=1000)
            {
                if ($rs['TradeType']==0 && $rs['DeductMoney'] !=2){
                    CommFun::handleCode('-412',$idemp_arr);//不允许撤单
                }
            }
        }
        //组装接口参数
        $privT009 = ['applyserial'=>$param['applyserial'],'tradeacco'=>empty($rs['TradeAcco'])?$uinfo['tradeacco']:$rs['TradeAcco'],'tradepassword'=>$uinfo['password']];
        $resT009 = $this->obj_hs->apiRequest('T009',$privT009);
        //在本地库记录
        if ($resT009['code'] == parent::HS_SUCC_CODE)
        {
            $res = $this->orderHandleApi($param['orderno'],$resT009['applyserial'],2,$uinfo['tradeacco'],0,0,$param['applyserial'],0,$rs['FundCode']);
        }else {
            $res = 0;
            $errlog = "撤单失败，接口返回:".var_export($resT009,true).'-提交参数:'.var_export($idemp_arr,true);
            Yii::error($errlog,__METHOD__);
        }
        unset($resT009['bankurl'], $resT009['capitalmode'], $resT009['form']);
        $idemp_arr['Oid'] = (int)$res;
        return ['hd_res'=>$resT009,'idemp_arr'=>$idemp_arr];
    }
    /**
     * 赎回处理函数
     * saleway 卖出方式：0:赎回到银行卡;1:转到现金宝;2:转到其他基金;3:T+0赎回;4:超级现金宝赎回
     * busintype 业务大类 现金宝取现：01；超级现金宝取现：02
     * @param array $param=['orderno'=>'订单号','bankacco'=>'银行卡号','tradepassword'=>'交易密码','applyshare'=>'申请份额','sharetype'=>'收费方式']
     */
    public function handleSale($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>3];
        $uinfo = $this->checkHandle($param,$idemp_arr,$param['bankacco']);
        //组装接口参数
        $fundInfo = $this->getFundInfo();
        if (empty($fundInfo))
        {
            CommFun::handleCode('-404',$idemp_arr);//获取不到基金数据
        }
        if (!in_array($fundInfo['FundState'],[1,2,7,0,8,5]))
        {
            CommFun::handleCode('-405',$idemp_arr);//基金非交易状态
        }
        $fundPosition = new FundPosition([],$this->merid);
        $rsFp = $fundPosition->query(" Uid = '{$this->uid}' AND FundCode = '{$this->fundCode}' ",'one');
        if (empty($rsFp) || empty($rsFp['CurrentRemainShare'])){
            CommFun::handleCode('-410',$idemp_arr);
        }else {
            if (bcsub($rsFp['CurrentRemainShare'], $rsFp['FreezeSellShare'],2) < $param['applyshare'])
            {
                CommFun::handleCode('-411',$idemp_arr);//赎回份额不能大于可用份额
            }else {
                $remain = bcsub($rsFp['CurrentRemainShare']-$rsFp['FreezeSellShare'], $param['applyshare'],2);
                if ($remain >0 && $remain < $fundInfo['MinHoldShare'])
                {
                    CommFun::handleCode('-415',$idemp_arr);//赎回后持有份额不能低于最低保留份额,请全部赎回'
                }
            }
        }
        //组装接口参数
        $privT006 = [
            'applysum' => $param['applyshare'],
            'tradeacco' => $uinfo['tradeacco'],
            'redeemuseflag' => '1',
            'saleway' => '0',
            'customdelayflag' => '0',
            'fundcode' => $this->fundCode,
            'sharetype' => $fundInfo['ShareType'],
            'mintredeem'=>isset($param['mintredeem'])?$param['mintredeem']:'1',
            ];
        //现金宝标示
        $moneyFund = 0;
        if (isset($param['busintype']) &&  $param['busintype'] == '01'){ //宝类快速取现
            $privT006['saleway'] = '3';
            $privT006['realtimeflag'] = '1';
            $privT006['transfermoney'] = '1';//0不划款 划款
            $moneyFund  =1;
        }
        $resT006 = $this->obj_hs->apiRequest('T006',$privT006);
        //在本地库记录
        if ($resT006['code'] == parent::HS_SUCC_CODE)
        {
            $rs = $this->orderHandleApi($param['orderno'], $resT006['applyserial'], 1, $uinfo['tradeacco'], 0, $param['applyshare'],'',$moneyFund);
        }else {
            $rs = 0;
            $errlog = "赎回失败，接口返回:".var_export($resT006,true).'-提交参数:'.var_export($idemp_arr,true);
            Yii::error($errlog,__METHOD__);
        }
        $idemp_arr['Oid'] = (int)$rs;
        return ['hd_res'=>$resT006,'idemp_arr'=>$idemp_arr];
    }

    /**
     * 获取个人信息数据
     * @param string $bankacco
     * @return array ['password'=>'交易密码(明文)','tradeacco'=>'交易账号']注:bankacco为空返回第一个银行卡对应交易账号 
     */
    private function getUserInfo($bankacco=NULL)
    {
        if (empty($bankacco))
        {
            $sql = "SELECT u.Pass,ub.TradeAcco FROM `user` u LEFT JOIN user_bank ub ON u.id = ub.Uid WHERE u.id= '{$this->uid}' ";
        }else {
            $sql = "SELECT u.Pass,ub.TradeAcco FROM `user` u LEFT JOIN user_bank ub ON u.id = ub.Uid WHERE u.id='{$this->uid}' AND ub.BankAcco = '{$bankacco}'";
        }
        $row = Yii::$app->db_local->createCommand($sql)->queryOne();
        if (!empty($row))
        {
            $arr['password'] = CommFun::AutoEncrypt($row['Pass'],'D');
            $arr['tradeacco'] = $row['TradeAcco'];
            return $arr;
        }else {
            Yii::error("个人信息查询失败sql:{$sql}",__METHOD__);
            return false;
        }
    }
    /**
     * 获取基金信息
     * @return array 见CommFun::GetFundInfo 返回值
     */
    public function getFundInfo()
    {
        $fundInfo = CommFun::GetFundInfo($this->fundCode);
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
        return $fundInfo;
    }

    /*
     * 订单处理
     * @param string $orderno 订单编号
     * @param string $applyserial 申请编号
     * @param int $type 0:买入 1:卖出 2:撤单 3:分红
     * @param string $tradeacco 交易账号
     * @param string $applyAmount 申请购买金额(买入)
     * @param string $applyShare 申请卖出份额(卖出)
     * @param string $originalApplyserial 原申请编号(撤单)
     * @param string $orFundCode 原撤单基金代码
     */
    public function orderHandleApi($orderno,$applyserial,$type,$tradeacco,$applyAmount=0,$applyShare=0,$originalApplyserial='',$moneyFund=0,$orFundCode='')
    {
        $datatime = date('Y-m-d H:i:s');
        $tradeDay = CommFun::getApplyTradeDay();
        if ($type ==0)
        {
            $orderField = ['OrderNo'=>$orderno, 'Uid'=>$this->uid, 'FundCode'=>$this->fundCode,'ApplyAmount'=>$applyAmount,'ApplyShare'=>$applyShare,
                'TradeType'=>0,'TradeAcco'=>$tradeacco,'ApplyTime'=>$datatime,'ApplySerial'=>$applyserial,'MoneyFund'=>$moneyFund,'TradeDay'=>$tradeDay
            ];
        }elseif ($type ==1)
        {
            $orderField = ['OrderNo'=>$orderno,'Uid'=>$this->uid,'FundCode'=>$this->fundCode,'ApplyAmount'=>$applyAmount,'ApplyShare'=>$applyShare,
                'TradeType'=>1,'TradeAcco'=>$tradeacco,'ApplyTime'=>$datatime,'ApplySerial'=>$applyserial,'MoneyFund'=>$moneyFund,'TradeDay'=>$tradeDay
            ];
        }elseif ($type ==2)
        {
            $orderField = ['OrderNo'=>$orderno,'Uid'=>$this->uid,'FundCode'=>$orFundCode,'TradeType'=>2,'TradeAcco'=>$tradeacco,'HandleStatus'=>1,'HandleTime'=>$datatime,
                'ApplyTime'=>$datatime,'ApplySerial'=>$applyserial,'OriginalApplyserial'=>$originalApplyserial,'TradeStatus'=>1,'TradeDay'=>$tradeDay];
        }else {
            return false;
        }
        return FundPosition::HandleOrderPosition($this->merid, $type, $orderField);
    }

    /*
     * 交易处理判断(购买、赎回、撤单公用函数)
     * @param array $param post提交参数
     * @param array ['Instid'=>'商户号','OrderNo'=>'订单号','Oid'=>'业务id','Type'=>'订单类型'] idempotence_order字段数据
     * @param string $bankacco 银行卡
     */
    public function checkHandle($param,$idemp=[],$bankacco=NULL)
    {
        $uinfo = $this->getUserInfo($bankacco);
        if (!empty($uinfo))
        {
            if (!$this->noNeedPass)
            {
                //判断交易密码
                if($uinfo['password'] != $param['tradepassword'])
                {
                    //交易密码错误
                    CommFun::handleCode('-8',$idemp);
                }
            }
            return $uinfo;
        }else {
            CommFun::handleCode('-200',$idemp);//用户id不存在
        }
    }
    /**
     * 定投新增处理
     * @param array post数据
     */
    public function HandleValutrade($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>1];
        $uinfo = $this->checkHandle($param,$idemp_arr);
        $resI006 = parent::doValuavgr($uinfo['password'], $param['applysum'], $param['cycleunit'], $uinfo['tradeacco'], $param['bankacco'], 
           $param['jyrq'],$param['zzrq'],$param['scjyrq'],$param['jyzq']);
        if ($resI006['code'] == parent::HS_SUCC_CODE)
        {
           //本地业务处理 
           $fundInfo = $this->getFundInfo();
           $valuField = ['Uid'=>$this->uid,'Xyh'=>$resI006['xyh'],'Applyserial'=>$resI006['applyserial'],'FundCode'=>$this->fundCode,'Applysum'=>$param['applysum'],
               'Cycleunit'=>$param['cycleunit'],'Jyrq'=>$param['jyrq'],'Scjyrq'=>$param['scjyrq'],'Sharetype'=>$fundInfo['ShareType'],
               'Tradeacco'=>$uinfo['tradeacco'],'Zzrq'=>$param['zzrq'],'Bankacco'=>$param['bankacco'],'Jyzq'=>$param['jyzq'],'State'=>'A','SysTime'=>date('Y-m-d H:i:s')
           ];
           $valuObj = new ValuTradePlan($valuField,$this->merid);
           $rs = $valuObj->insert();
        }else {
            $rs = 0;
        }
        $idemp_arr['Oid'] = $rs;
        return ['hd_res'=>$resI006,'idemp_arr'=>$idemp_arr];
    }
    /**
     * 定投变更处理
     * @param array post数据['tradepassword'=>'交易密码','xyh'=>'协议号','state'=>'协议状态','zzrq'=>'终止日期','jyzq'=>'交易周期','jyrq'=>'交易日期','cycleunit'=>'周期单位',
     * 'applysum'=>'金额']
     */
    public function HandleValutradechange($param)
    {
        $uInfo = $this->getUserInfo();
        $this->checkHandle($param);
        $privI007 = ['tradepassword'=>$param['tradepassword'],'xyh'=>$param['xyh'],'state'=>$param['state'],'zzrq'=>$param['zzrq'],'jyzq'=>$param['jyzq'],
            'jyrq'=>$param['jyrq'],'cycleunit'=>$param['cycleunit'],'applysum'=>$param['applysum'],'tradeacco'=>$uInfo['tradeacco']];
        $resI007 = $this->obj_hs->apiRequest('I007',$privI007);
        if ($resI007['code'] == parent::HS_SUCC_CODE)
        {
            //本地业务处理
            $valuField = ['Xyh'=>$param['xyh'],'Applysum'=>$param['applysum'],'Cycleunit'=>$param['cycleunit'],'Jyrq'=>$param['jyrq'],
                'Zzrq'=>$param['zzrq'],'Jyzq'=>$param['jyzq'],'State'=>$param['state'],'SysTime'=>date('Y-m-d H:i:s')
            ];
            $valuObj = new ValuTradePlan($valuField,$this->merid);
            $where = " xyh={$param['xyh']}";
            try {
                $valuObj->update($where);
            } catch (Exception $e) {
                \Yii::error($e->getMessage(),__METHOD__);
            }
            
        }
        return $resI007;
    }
    /**
     * 修改分红方式处理
     * @param array post数据[]
     */
    public function HandleBonus($param)
    {
        $this->checkHandle($param);
        $uInfo = $this->getUserInfo();
        $fundInfo = $this->getFundInfo();
        $privT007 = ['fundcode'=>$this->fundCode,'melonmethod'=>$param['melonmethod'],'sharetype'=>$fundInfo['ShareType'],
            'tradeacco'=>$uInfo['tradeacco'],'tradepassword'=>$this->noNeedPass?$uInfo['password']:$param['tradepassword']
        ];
        $resT007 = $this->obj_hs->apiRequest('T007',$privT007);
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>9];
        return ['hd_res'=>$resT007,'idemp_arr'=>$idemp_arr];
    }
}