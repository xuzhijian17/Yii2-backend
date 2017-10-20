<?php
namespace frontend\modules\api\services;

use frontend\models\IdempotenceOrder;
use frontend\modules\api\models\PortfolioConfig;
use frontend\modules\api\models\PortfolioTrade;
use Yii;
use common\lib\CommFun;
use frontend\models\TradeOrder;
use frontend\services\TradeService;
use frontend\models\ValuTradePlan;
use common\models\PortfolioPosition;

/**
 * 组合交易相关类(申购、赎回、撤单等业务实现)
 */
class PortfolioServiceApi extends TradeServiceApi
{
    public $portfolioid ; //组合编号id

    /*
     * 构造方法
     * @param int $uid 用户id
     * @param int $merid 商户号
     * @param string $portfolioid 组合编号代码
     */
    function __construct($uid, $merid, $portfolioid = '')
    {
        $this->portfolioid = $portfolioid;
        parent::__construct($uid, $merid);
    }

    /**
     * 组合申购处理函数
     *
     * @param array $param=['orderno'=>'订单号','bankacco'=>'银行卡号','applysum'=>'申请金额','tradepassword'=>'交易密码']
     * @return array 处理结果
     */
    public function handlePortfolioPurchase($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>IdempotenceOrder::PORTFOLIO_PURCHASE];
        $uinfo = $this->checkHandle($param,$idemp_arr,$param['bankacco']);
        $portfolio = PortfolioConfig::getPortfolioById($this->portfolioid);
        if (empty($portfolio)) {
            CommFun::handleCode('-504',$idemp_arr);//获取不到组合数据
        }

        if ($portfolio['Status'] != 1) {   //状态：0-未上线，1-已上线，-1-删除
            CommFun::handleCode('-501',$idemp_arr); //组合不存在
        }

        if ($param['applysum'] < $portfolio['MinSum']) {
            CommFun::handleCode('-505',$idemp_arr);    //组合申请金额小于组合起购金额
        }

        $fund_list = $portfolio['FundList'];
        $T003_params = [];
        foreach ($fund_list as $key=>$val) {
            $this->fundCode = $val['fundcode'];
            $fundInfo = $this->getFundInfo(); //校验基金是否处于可申购状态
            if (empty($fundInfo)) {
                CommFun::handleCode('-404',$idemp_arr); //获取不到基金数据
            }
            if (empty($fundInfo['businflag'])) {
                CommFun::handleCode('-405',$idemp_arr); //基金处于非交易状态
            }

            $applysum = round($param['applysum'] * $val['ratio']/100, 2);
            if ($applysum < $fundInfo['MinPurchaseAmount']) {
                CommFun::handleCode('-500',$idemp_arr);    //组合申请金额小于组合起购金额
            }
            $privT003 = [
                'applysum'  => $applysum,
                'businflag' => $fundInfo['businflag'],
                'fundcode'  => $this->fundCode,
                'sharetype' => $fundInfo['ShareType'],
                'tradeacco' => $uinfo['tradeacco'],
                'tradepassword' => $uinfo['password'],
                'detailcapitalmode' => parent::DETAILCAPITALMODE
            ];

            $T003_params[] = $privT003;
        }
        $portfolioTradeId = $this->insertPortfolioTrade($param['applysum'], PortfolioTrade::TT_PURCHASE); //插入组合交易记录
        $error_code = $error_message = "";
        $rsStatus = -1; //初始为-1，失败，有任何一笔申请成功则为1，成功
        foreach ($T003_params as $key=>$priv_params) {
            $this->fundCode = $priv_params['fundcode'];
            $resT003 = $this->obj_hs->apiRequest('T003', $priv_params);
            //在本地库记录
            if ($resT003['code'] == parent::HS_SUCC_CODE) {
               $rs = $this->portfolioOrderHandleApi($param['orderno'], $resT003['applyserial'], PortfolioTrade::TT_PURCHASE,
                           $portfolioTradeId, $uinfo['tradeacco'], $priv_params['applysum']);
                $rsStatus = 1;
            } else {
                $error_code = $resT003['code'];
                $error_message = $resT003['message'];
                Yii::info(array_merge(['uid'=>$this->uid, 'code'=>$resT003['code'], 'message'=>$resT003['message']], $priv_params), 'info');//记录日志
            }

        }

        if ($rsStatus == 1) {
            $code = parent::HS_SUCC_CODE;
            $message = "order success";
        } else {
            $code = $error_code;
            $message = $error_message;
        }

        $idemp_arr['Oid'] = $portfolioTradeId;
        return ['hd_res'=>['code'=>$code, "message"=>$message, 'orderno'=>$param['orderno'], 'portfolioid'=>$this->portfolioid, "portfoliotradeid"=>$portfolioTradeId],'idemp_arr'=>$idemp_arr];
    }

    /*
     * 组合撤单处理函数
     * @param array $param=['orderno'=>'订单号','applyserial'=>'申请编号','tradepassword'=>'交易密码']
     * @return array
     */
    public function handlePortfolioWithDraw($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>IdempotenceOrder::PORTFOLIO_WITHDRAW];
        $uinfo = $this->checkHandle($param,$idemp_arr);

        $portfolioTrade = new PortfolioTrade($this->merid);
        $tradeInfo = $portfolioTrade->getPortfolioTradeById($param['portfoliotradeid']);

        if (empty($tradeInfo)) {
            CommFun::handleCode('-506',$idemp_arr); //不存在交易记录
        }

        /**
         * ??这块缺一个查询交易的所属工作日，用于校验在所属工作日3点之前可撤单，否则不可撤单
         */

        $tradeOrder = new TradeOrder([], $this->merid);
        $tradeOrderList = $tradeOrder->query("PortfTradeId='{$tradeInfo['id']}' AND ApplySerial!=''", "all");
        if (empty($tradeOrderList)) {
            CommFun::handleCode('-506',$idemp_arr);
        }

        $portfolioTradeId = $this->insertPortfolioTrade($tradeInfo['ApplyAmount'], PortfolioTrade::TT_WITHDRAW, 0, $tradeInfo['id']); //插入组合交易记录

        $rsStatus = -1; //初始为-1，失败，有任何一笔申请成功则为1，成功

        foreach ($tradeOrderList as $key=>$val) {
            $privT009 = [   //组装接口参数
                'applyserial' => $val['ApplySerial'],
                'tradeacco' => empty($val['TradeAcco']) ? $uinfo['tradeacco'] : $val['TradeAcco'],
                'tradepassword' => $uinfo['password']
            ];

            $resT009 = $this->obj_hs->apiRequest('T009', $privT009);

            if ($resT009['code'] == parent::HS_SUCC_CODE) {  //在本地库记录
                $rs = $this->portfolioOrderHandleApi($param['orderno'], $resT009['applyserial'], PortfolioTrade::TT_WITHDRAW,
                           $portfolioTradeId, $uinfo['tradeacco'], 0, 0, $val['ApplySerial']);
                $rsStatus = 1;
            } else {
                $error_code = $resT009['code'];
                $error_message = $resT009['message'];

                Yii::info(array_merge(['uid'=>$this->uid, 'code'=>$resT009['code'], 'message'=>$resT009['message']], $privT009), 'info');//记录日志
            }
        }

        if ($rsStatus == 1) {
            $code = parent::HS_SUCC_CODE;
            $message = "order success";
        } else {
            $code = $error_code;
            $message = $error_message;
        }

        $idemp_arr['Oid'] = $portfolioTradeId;
        return ['hd_res'=>['code'=>$code, "message"=>$message, 'orderno'=>$param['orderno'], 'portfolioid'=>$this->portfolioid, "portfoliotradeid"=>$portfolioTradeId],'idemp_arr'=>$idemp_arr];
    }

    /*
     * 组合赎回处理函数
     * saleway 卖出方式：0:赎回到银行卡;
     * busintype 业务大类
     * @param array $param=['orderno'=>'订单号','bankacco'=>'银行卡号','tradepassword'=>'交易密码','portfolioid'=>'组合编号','ratio'=>'赎回占比']
     */
    public function handlePortfolioSale($param)
    {
        //生成订单参数
        $idemp_arr = ['Instid'=>$this->merid,'OrderNo'=>$param['orderno'],'Oid'=>0,'Type'=>IdempotenceOrder::PORTFOLIO_SALE];
        $uinfo = $this->checkHandle($param,$idemp_arr,$param['bankacco']);
        $PositionObj = new PortfolioPosition([], $this->merid);

        $portfolioPosition = $PositionObj->query("PortfolioId='{$this->portfolioid}'", "all");
        if (empty($portfolioPosition)) {
            CommFun::handleCode('-507',$idemp_arr); //获取不到该组合持仓数据
        }

        $portfolioTradeId = $this->insertPortfolioTrade(0, PortfolioTrade::TT_SELL, $param['ratio']); //插入组合交易记录
        $rsStatus = -1; //初始为-1，失败，有任何一笔申请成功则为1，成功
        $error_code = $error_message = "";
        foreach ($portfolioPosition as $key=>$val) {
            $this->fundCode = $val['FundCode'];
            $fundInfo = $this->getFundInfo();
            if (empty($fundInfo)) {
                CommFun::handleCode('-404',$idemp_arr);//获取不到基金数据
            }
            $applyshare = sprintf("%.2f", $param['applyshare'] * $param['ratio']/100);
            $privT006 = [ //组装接口参数
                'applysum' => $applyshare,
                'tradeacco' => $uinfo['tradeacco'],
                'redeemuseflag' => '1',
                'saleway' => '0',
                'customdelayflag' => '0',
                'fundcode' => $this->fundCode,
                'sharetype' => $fundInfo['sharetype'],
            ];

            $resT006 = $this->obj_hs->apiRequest('T006', $privT006);
            if ($resT006['code'] == parent::HS_SUCC_CODE) { //在本地库记录
                $rs = $this->portfolioOrderHandleApi($param['orderno'], $resT006['applyserial'], PortfolioTrade::TT_SELL,
                                $portfolioTradeId, $uinfo['tradeacco'], 0, $param['applyshare']);
                $rsStatus = 1;
            } else {
                $error_code = $resT006['code'];
                $error_message = $resT006['message'];
                Yii::info(array_merge(['uid'=>$this->uid, 'code'=>$resT006['code'], 'message'=>$resT006['message']], $privT006), 'info');//记录日志
            }
        }
        if ($rsStatus == 1) {
            $code = parent::HS_SUCC_CODE;
            $message = "order success";
        } else {
            $code = $error_code;
            $message = $error_message;
        }
        $idemp_arr['Oid'] = $portfolioTradeId;
        //生成订单
        $idempOrderField = ['OrderNo'=>$param['orderno'], 'Oid'=>$portfolioTradeId, 'Type'=>IdempotenceOrder::PORTFOLIO_SALE, 'Status'=>$rsStatus];
        CommFun::createIdempotenceOrder($this->merid, $idempOrderField);
        return ['hd_res'=>['code'=>$code, "message"=>$message, 'orderno'=>$param['orderno'], 'portfolioid'=>$this->portfolioid, "portfoliotradeid"=>$portfolioTradeId],'idemp_arr'=>$idemp_arr];
    }
    
    /*
     * 组合订单处理
     * @param string $orderno 订单编号
     * @param string $applyserial 申请编号
     * @param int $type 0:买入 1:卖出 2:撤单 3:分红
     * @param int $porttradeId 组合交易记录的自增id
     * @param string $tradeacco 交易账号
     * @param string $applyAmount 申请购买金额(买入)
     * @param string $applyShare 申请卖出份额(卖出)
     * @param string $originalApplyserial 原申请编号(撤单)
     */
    public function portfolioOrderHandleApi($orderno, $applyserial, $type, $porttradeId, $tradeacco, $applyAmount=0, $applyShare=0, $originalApplyserial='')
    {
        $datatime = date('Y-m-d H:i:s');
        if (!in_array($type, [0, 1, 2])) {
            return false;
        }
        $orderno = $orderno.'_'.rand(0,20);
        $orderField = ['OrderNo'=>$orderno, 'Uid'=>$this->uid, 'FundCode'=>$this->fundCode, 'ApplyAmount'=>$applyAmount,
            'ApplyShare'=>$applyShare, 'TradeType'=>$type, 'TradeAcco'=>$tradeacco, 'ApplyTime'=>$datatime,
            'ApplySerial'=>$applyserial, 'PortfTradeId'=>$porttradeId
        ];
        if ($type == 2){
            $orderField['OriginalApplyserial'] = $originalApplyserial;
            $orderField['TradeStatus'] = 1;
        }
        return PortfolioPosition::HandleOrderPosition($this->merid, $this->portfolioid, $type, $orderField);
    }

    /*
     * 插入组合交易记录
     * @param $applyamount
     * @param $tradetype
     * @param int $ratio
     * @param int $originalid
     * @return PortfolioTrade
     */
    public function insertPortfolioTrade($applyamount, $tradetype, $ratio=0, $originalId=0)
    {
        $date = date("Y-m-d H:i:s");
        $db_local = Yii::$app->db_local;
        $sql = /** @lang text */ "INSERT INTO portfolio_trade_{$this->merid} SET Uid='{$this->uid}',PortfolioId='{$this->portfolioid}',ApplyAmount='{$applyamount}',ConfirmAmount='0',";
        $sql .= /** @lang text */ "Ratio='{$ratio}',TradeStatus='".PortfolioTrade::TS_APPLYING."',TradeType='{$tradetype}',OriginalId='{$originalId}',HandleStatus='".PortfolioTrade::HS_UNPROCESS."',ApplyTime='{$date}'";
        $db_local->createCommand($sql)->execute(); //交易记录表
        $last_trade_id = $db_local->getLastInsertID();
        return $last_trade_id;
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

        if (!empty($param['startdate'])) {
            $start_time = date("Y-m-d H:i:s", strtotime($param['startdate'])); //历史交易开始日期,格式yyyymmdd
            $where .= " AND ApplyTime>='{$start_time}' ";
        }
        if (!empty($param['enddate'])) {
            $start_time = date("Y-m-d H:i:s", strtotime($param['enddate'])); //历史交易结束日期,格式yyyymmdd
            $where .= " AND ApplyTime<='{$start_time}' ";
        }
        if (!empty($param['portfoliotradeid'])) {
            $where .= " AND id='{$param['portfoliotradeid']}' ";
        }

        $portTrade = new PortfolioTrade($this->merid); //组合交易模型类
        $trade_list = $portTrade->query($where, 'all', 'order by ApplyTime desc', $limit);
        $proftradeids = array_keys($trade_list);
        $trade_list = array_values($trade_list);

        $tradeorder = new TradeOrder($this->uid, $this->merid); //单支基金交易模型类
        $trade_detail_list = $tradeorder->getPortfolioTradeOrderGroup($this->uid, implode(',', $proftradeids));
        foreach ($trade_list as $key=>$val) {
            $val['portftradeid'] = $val['id'];
            if (isset($trade_detail_list[$val['id']])) {
                $val['trade_detail'] = $trade_detail_list[$val['id']];
            }
            unset($val['id']);
            $trade_list[$key] = $val;
        }
        $totalrecords = $portTrade->getRecordsCount($where);

        $result = [
            'code' => 'ETS-5BP0000',
            'message' => 'get success',
            'totalrecords' => $totalrecords,
            'list' => $trade_list
        ];
        return $result;
    }
}