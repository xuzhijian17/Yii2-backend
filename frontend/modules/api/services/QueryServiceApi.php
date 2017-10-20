<?php
namespace frontend\modules\api\services;
use common\models\FundPosition;
use frontend\modules\api\models\BankInfo;
use frontend\modules\api\models\UserBank;
use Yii;
use common\lib\CommFun;
use frontend\models\TradeOrder;
use frontend\services\TradeService;

/**
 * 查询业务处理类
 * 包含：交易申请、份额查询、交易确认查询等
 *
 * Class QueryServiceApi
 * @package frontend\modules\api\services
 */
class QueryServiceApi extends TradeService
{
    /*
     * 构造方法
     * @param int $uid 用户id
     * @param int $merid 商户号
     * @param string $fundCode 基金代码
     */
    public function __construct ( $uid, $merid, $fundCode='' )
    {
        parent::__construct ( $uid, $merid, $fundCode );
    }

    /*
     * 处理份额查询业务
     *
     * @param $param
     */
    public function handleShareQuery ( $param )
    {
        //组装接口参数
        $privS001 = [
                    'querytype' => 1,
                    ];

        if (!empty($param['fundcode'])) {
            $privS001['fundcode'] = $param['fundcode'];
        }

        $resS001 = $this->obj_hs->apiRequest('S001', $privS001);

        if ($resS001['code'] == parent::HS_SUCC_CODE) { //在本地库记录
           // $this->orderHandleApi($param['orderno'],$resT009['applyserial'],2,$uinfo['tradeacco'],0,0,$param['applyserial']);
        }
        return $resS001;
    }

    /*
     * 处理份额查询业务第二版，直查本地库
     *
     * @param $param
     */
    public function handleShareQueryV2 ( $param )
    {
        $where = "uid = '{$this->uid}'";

        if (!empty($param['fundcode'])) {
            $where .= " AND FundCode='{$param['fundcode']}'";
        }
        $fundPosition = new FundPosition([], $this->merid);
        $position_list = $fundPosition->query($where, 'all');
        foreach ($position_list AS $key=>$val ) {
            $val = array_change_key_case($val, CASE_LOWER);
            $fundInfo = CommFun::GetFundInfo($val['fundcode']);
            $val['fundname'] = !empty($fundInfo) ? $fundInfo['FundName'] : '';
            $val['pernetvalue'] = $fundInfo['PernetValue'];
            $position_list[$key] = $val;
        }
        $total_sum = $fundPosition->getUserTotalPositionByUid($this->uid);
        $result = [
            'code' => 'ETS-5BP0000',
            'message' => 'get success',
            'totalsum'=>empty($total_sum['sum'])?'0.00':$total_sum['sum'],
            'sumdayprofitloss' =>empty($total_sum['sumdayprofitloss'])?'0.00':$total_sum['sumdayprofitloss'],
            'sumtotalprofitloss' =>empty($total_sum['sumtotalprofitloss'])?'0.00':$total_sum['sumtotalprofitloss'],
            'list' => $position_list
        ];
        return $result;
    }

    /*
     * 处理交易申请查询业务
     * @param $param
     */
    public function handleTradeApplyQuery ( $param )
    {
        //组装接口参数
        $privS003 = [
                 'applyrecordno' => $param['applyrecordno'], //每页记录数
                ];

        if (!empty($param['pageno'])) {
            $privS003['pageno'] = $param['pageno']; //页码
        }
        if (!empty($param['startdate'])) {
            $privS003['startdate'] = $param['startdate'];   //历史交易开始日期,格式yyyymmdd
        }
        if (!empty($param['enddate'])) {
            $privS003['enddate'] = $param['enddate'];   //历史交易结束日期
        }
        if (!empty($param['applyserial'])) {
            $privS003['applyserial'] = $param['applyserial']; //交易系统申请编号
        }
        if (!empty($param['xyh'])) {
            $privS003['xyh'] = $param['xyh'];   //定投协议号
        }

        $resS003 = $this->obj_hs->apiRequest('S003', $privS003);

        if ($resS003['code'] == parent::HS_SUCC_CODE) { //在本地库记录
            // $this->orderHandleApi($param['orderno'],$resT009['applyserial'],2,$uinfo['tradeacco'],0,0,$param['applyserial']);
        }
        return $resS003;
    }

    /*
     * 处理交易确认查询业务
     * @param $param
     */
    public function handleTradeConfirmQuery ( $param )
    {
        //组装接口参数
        $privS004 = [
            'applyrecordno' => $param['applyrecordno'], //每页记录数
        ];

        if (!empty($param['pageno'])) {
            $privS004['pageno'] = $param['pageno']; //页码
        }
        if (!empty($param['fundcode'])) {
            $privS004['fundcode'] = $param['fundcode']; //基金代码
        }
        if (!empty($param['callingcode'])) {
            $privS004['callingcode'] = $param['callingcode']; //业务代码
        }
        if (!empty($param['startdate'])) {
            $privS004['startdate'] = $param['startdate'];   //历史交易开始日期,格式yyyymmdd
        }
        if (!empty($param['enddate'])) {
            $privS004['enddate'] = $param['enddate'];   //历史交易结束日期
        }
        if (!empty($param['requestno'])) {
            $privS004['requestno'] = $param['requestno']; //交易系统申请编号
        }
        if (!empty($param['xyh'])) {
            $privS004['xyh'] = $param['xyh'];   //定投协议号
        }

        $resS004 = $this->obj_hs->apiRequest('S004', $privS004);

        if ($resS004['code'] == parent::HS_SUCC_CODE) { //在本地库记录
            // $this->orderHandleApi($param['orderno'],$resT009['applyserial'],2,$uinfo['tradeacco'],0,0,$param['applyserial']);
        }
        return $resS004;
    }

    /*
     * 处理交易申请查询业务
     * @param $param
     */
    public function handleTradeRecordQuery ( $param )
    {
        $where = "uid = '{$this->uid}'";
        $page = !empty($param['pageno']) ? $param['pageno'] : 0;
        $limit = "";
        if (!empty($param['applyrecordno'])) {
            $size = intval($param['applyrecordno']);
            $page = ($page - 1) * $size;
            if ($page<0) {
                $page = 0;
            }
            $limit = " LIMIT {$page},{$size}";
        }
        if (!empty($param['orderno'])) {
            $where .= " AND Orderno='{$param['orderno']}' ";
        }
        
        if (!empty($param['startdate'])) {
            $start_time = date("Y-m-d H:i:s", strtotime($param['startdate'])); //历史交易开始日期,格式yyyymmdd
            $where .= " AND ApplyTime>='{$start_time}' ";
        }
        if (!empty($param['enddate'])) {
            $start_time = date("Y-m-d H:i:s", strtotime($param['enddate'])); //历史交易结束日期,格式yyyymmdd
            $where .= " AND ApplyTime<='{$start_time}' ";
        }
        if (!empty($param['applyserial'])) {
            $where .= " AND ApplySerial='{$param['applyserial']}' ";    //交易系统申请编号
        }
        if (!empty($param['xyh'])) {
            $where .= " AND xyh='{$param['xyh']}' ";    //定投协议号
        }
        //过滤撤单记录
        if (!empty($param['nowithdraw'])){
            $where .= " AND TradeType !=2 ";
        }
        $tradeorder = new TradeOrder($this->uid, $this->merid);

        $trade_list_temp = $tradeorder->query($where, 'all', 'order by ApplyTime desc', $limit);

        $totalrecords = $tradeorder->getTradeOrderCount($where);
        $user_bank = UserBank::getUserBankByUid($this->uid);
        $trade_list = [];

        if (!empty($trade_list_temp)) {
            $fund_code_array = [];
            foreach ($trade_list_temp AS $key=>$val ) {
                $fund_code_array[] = $val['FundCode'];
            }
            $fund_name_map = CommFun::getFundNameByFundCodes(implode("','", $fund_code_array));
            $tradeAcco = $trade_list_temp[0]['TradeAcco'];
            $bank_info = BankInfo::getBankQuotaInfo($user_bank[$tradeAcco]['BankSerial']); //获取银行信息
        }

        foreach ($trade_list_temp AS $key=>$val ) {
            $info = [
                'orderno' => $val['OrderNo'],
                'applytime' => $val['ApplyTime'],
                'confirmtime' => $val['ConfirmTime'],
                'tradetype' => $val['TradeType'],
                'tradestatus' => $val['TradeStatus'],
                'applyshare' => $val['ApplyShare'],
                'applysum' => $val['ApplyAmount'],
                'confirmshare' => $val['ConfirmShare'],
                'confirmsum' => $val['ConfirmAmount'],
                'netvalue' => $val['ConfirmNetValue'],
                'tradefee' => $val['Poundage'],
                'fundcode' => $val['FundCode'],
                'fundname' => isset($fund_name_map[$val['FundCode']]) ? $fund_name_map[$val['FundCode']]['FundName'] : '',
                'melonmethod' => 0, //分红方式待修改分红方式开发完后加上
                'bankname' => !empty($bank_info) ? $bank_info['bankname'] : '',
                'bankacco' => $user_bank[$val['TradeAcco']]['BankAcco'],
                'bankserial' => $user_bank[$val['TradeAcco']]['BankSerial'],
                'applyserial'=> $val['ApplySerial'],
                'deductmoney'=> $val['DeductMoney'],
                'xyh' => $val['Xyh'], //协议号定投的，待定投的开发完之后加上
                'otherinfo' => $val['OtherInfo'],
            ];
            $trade_list[] = $info;
        }
        $result = [
                'code' => 'ETS-5BP0000',
                'message' => 'get success',
                'totalrecords' => $totalrecords,
                'list' => $trade_list
                ];
        return $result;
    }

    /*
     * 查询历史分红接口
     * @param array $post 传递参数
     */
    public function getHisBonusList($post)
    {
        $privS005 = ['applyrecordno'=>$post['applyrecordno'],'startdate'=>isset($post['startdate'])?$post['startdate']:'',
            'enddate'=>isset($post['enddate'])?$post['enddate']:'','pageno'=>isset($post['pageno'])?$post['pageno']:'1'
        ];
        if(!empty($post['fundcode'])) {
           $privS005['fundcode'] = $post['fundcode'];
        }
        
        $resS005 = $this->obj_hs->apiRequest('S005',$privS005);
        return $resS005;
    }

    /*
     * 订单状态查询
     * @param array 请求数组
     */
    public static function GetOrderStatus($param)
    {
        $db_local = Yii::$app->db_local;
        $rs = $db_local->createCommand("SELECT * FROM `idempotence_order_{$param['instid']}` WHERE OrderNo = '{$param['orderno']}'")->queryOne();
        if (empty($rs))
        {
            CommFun::handleCode('-5');//无此订单请求
        }else {
            return ['code'=>$rs['Code'],'message'=>$rs['Message'],'type'=>$rs['Type'],'custom'=>1];
        }
    }

    /*
     * 基金交易限制查询
     * @param string $fundcode 基金代码
     * @return array ['minholdshare'=>'最小持有份额','minredemeshare'=>'最低赎回份额','minpurchaseamount'=>'最低申购金额',
     * 'minsubscribamount'=>'最低认购金额','minaddpurchaseamount'=>'申购追加最小值','minvaluagramount'=>'最低定投金额',
     * 'minaddvaluagramount'=>'最低定投追加金额','fundstatus'=>'基金状态']
     */
    public static function GetLimitInfo($fundcode)
    {
        $fundInfo = CommFun::GetFundInfo($fundcode);
        if (empty($fundInfo))
        {
            CommFun::handleCode('-404');//取不到基金数据
        }else {
            $limitArr['minholdshare'] = $fundInfo['MinHoldShare'];//最低持有
            $limitArr['minredemeshare'] = $fundInfo['MinRedemeShare'];//最低赎回
            $limitArr['minpurchaseamount'] = $fundInfo['MinPurchaseAmount'];//最低申购金额
            $limitArr['minsubscribamount'] = $fundInfo['MinSubscribAmount'];//最低认购
            $limitArr['minaddpurchaseamount'] = $fundInfo['MinAddPurchaseAmount'];//最低申购追加
            $limitArr['minvaluagramount'] = $fundInfo['MinValuagrAmount'];
            $limitArr['minaddvaluagramount'] = $fundInfo['MinAddValuagrAmount'];
//             //最低赎回 *净值*1.1
//             $mrshare = $fundInfo['MinRedemeShare']*$fundInfo['PernetValue']*1.1;
//             //最低持有*净值*1.1
//             $mhshare = $fundInfo['MinHoldShare']*$fundInfo['PernetValue']*1.1;
//             $mpamount = max($mhshare,$mrshare,$limitArr['minpurchaseamount']);
//             if($mpamount <100)
//             {
//                 $limitArr['minpurchaseamount'] = ceil($mpamount/10)*10;
//             }else{
//                 $limitArr['minpurchaseamount'] = ceil($mpamount/100)*100;
//             }
            $limitArr['fundstatus'] = $fundInfo['FundState'];
            $limitArr['buystatus'] = in_array($fundInfo['FundState'],[1,2,6,7,0,8])?'1':'0';//可买状态1:可买0:不可买
            $limitArr['sellstatus'] = in_array($fundInfo['FundState'],[1,2,7,0,8,5])?'1':'0';//可卖状态1:可卖0:不可卖
            $limitArr['code'] = self::HS_SUCC_CODE;
            $limitArr['message'] = '请求成功';
            return $limitArr;
        }
    }

    public static function getFundDividendList($fundcode)
    {
        $fundInfo = CommFun::GetFundInfo($fundcode);
        if (empty($fundInfo)) {
            CommFun::handleCode('-404');//取不到基金数据
        } else {
            $db_local = Yii::$app->db_juyuan;
            $sql = "SELECT ExecuteDate as meloncutting,ReDate as enrolldate,ROUND(ActualRatioAfterTax/10,4) as shareperbonus,ExRightDate as exrightdate FROM MF_Dividend WHERE InnerCode='{$fundInfo['InnerCode']}' ORDER BY ReDate DESC";
            $list = $db_local->createCommand($sql)->queryAll();
            $result = [];
            $result['code'] = self::HS_SUCC_CODE;
            $result['message'] = '请求成功';
            $result['list'] = $list;
            return $result;
        }
    }
}