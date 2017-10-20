<?php
namespace console\commands;

use Yii;
use common\models\FundPosition;
use console\models\Valutrade;
use common\lib\HundSun;
use common\lib\CommFun;
use Exception;

class ToolController extends Controller
{
    /**
     * 错误数据补充处理handle_err
     */
    public function actionLoophandle()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $handleArr = $db_local->createCommand("SELECT * FROM handle_err WHERE `Status` = 0")->queryAll();
        if (!empty($handleArr))
        {
            $datatime = date('Y-m-d H:i:s');
            foreach ($handleArr as $val)
            {
                if ($val['OidType'] ==0)
                {
                    //OidType=0 目标 trade_order
                    $res = FundPosition::makeUpConfirmBack($val['Oid'], $val['Instid'], $db_local);
                    if ($res){
                        $db_local->createCommand("UPDATE handle_err SET `Status` = 1 ,SystemTime = '{$datatime}' WHERE id ={$val['id']}")->execute();
                    }
                }elseif ($val['OidType'] ==1)
                {
                    //OidType=1 目标 fund_position
                    $fundposition = new FundPosition([], $val['Instid']);
                    $where = "id='{$val['Oid']}'";
                    $data = $fundposition->query($where, 'one', 'id asc', '1');

                    $queryInfoControll = new QueryinfoController();
                    $res = $queryInfoControll->queryInfoFromHs($data['Uid'], $data['FundCode'], $data['id'], $val['Instid'], false);
                    if ($res){
                        $db_local->createCommand("UPDATE handle_err SET `Status` = 1 ,SystemTime = '{$datatime}' WHERE id ={$val['id']}")->execute();
                    }
                }elseif ($val['OidType'] ==2)
                {
                    //OidType=2 目标 valutrade_plan
                    $valObj = new Valutrade();
                    $res = $valObj->makeUpValu($val['Oid'], $val['Instid'], $db_local);
                    if ($res){
                        $db_local->createCommand("UPDATE handle_err SET `Status` = 1 ,SystemTime = '{$datatime}' WHERE id ={$val['id']}")->execute();
                    }
                }else {
                    continue;
                }
            }
        }
    }
    /**
     * 节前两天，货基/短期理财不能申购
     */
    public function actionSuspend()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $redis = Yii::$app->redis;
        $logFile = 'Tool_'.date('Ymd').'.log';
        $fundArr = $db_local->createCommand("SELECT * FROM `fund_info` WHERE FundTypeCode = '1109' OR FundTypeCode='1106'")->queryAll();
        if (!empty($fundArr))
        {
            $n=0;
            $t1 = time();
            foreach ($fundArr as $val)
            {
                $sql = "UPDATE fund_info SET FundState = '5' WHERE FundCode = '{$val['FundCode']}'";
                $db_local->createCommand($sql)->execute();
                parent::commandLog("更新fund_info成功 sql:{$sql}",0,$logFile);
                $n++;
            }
            $loginfo = '共更新'.$n.'条数据 耗时'.(time()-$t1).' s';
            parent::commandLog($loginfo,0,$logFile);
            unset($val);
        }
        //更新缓存
        $fundArrNew = $db_local->createCommand("SELECT * FROM `fund_info` WHERE FundTypeCode = '1109' OR FundTypeCode='1106'")->queryAll();
        if (!empty($fundArrNew))
        {
            foreach ($fundArrNew as $val)
            {
                $redis->hset('fund_info',$val['FundCode'],json_encode($val,JSON_UNESCAPED_UNICODE));
            }
        }
    }
    /**
     * 货基/短期理财恢复申购
     */
    public function actionRecover()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $redis = Yii::$app->redis;
        $logFile = 'Tool_'.date('Ymd').'.log';
        $fundArr = $db_local->createCommand("SELECT * FROM `fund_info` WHERE FundTypeCode = '1109' OR FundTypeCode='1106'")->queryAll();
        if (!empty($fundArr))
        {
            $n=0;
            $t1 = time();
            $objHs = new HundSun(0,null,1);
            foreach ($fundArr as $val)
            {
                $resS010 = $objHs->apiRequest('S010',['fundcode'=>$val['FundCode']],false);
                if ($resS010['code'] == HundSun::SUCC_CODE && !empty($resS010['returnlist'][0]))
                {
                    $subScribeState = $resS010['returnlist'][0]['subscribestate'];//认购状态
                    $declareState = $resS010['returnlist'][0]['subscribestate'];//申购状态
                    $valuagrState = $resS010['returnlist'][0]['valuagrstate'];//定投状态
                    $withDrawState = $resS010['returnlist'][0]['withdrawstate'];//赎回状态
                    $minHoldShare = $resS010['returnlist'][0]['minshare'];//最小持有份额
                    $fundState = $resS010['returnlist'][0]['fundstate'];//基金状态
                }else {
                    parent::commandLog("S010查询出错fundcode:{$val['FundCode']}",1,$logFile);
                    $subScribeState=$declareState=$valuagrState=$withDrawState=$minHoldShare=$fundState=-404;
                }
                //S010查询基金信息--end//
                $fundInfoParam = ['DeclareState'=>$declareState,'SubScribeState'=>$subScribeState,'ValuagrState'=>$valuagrState,'WithDrawState'=>$withDrawState,
                    'MinHoldShare'=>$minHoldShare,'FundState'=>$fundState,'SysTime'=>date('Y-m-d H:i:s')
                ];
                $fundInfoField = CommFun::JoinUpdateStr($fundInfoParam,1);
                $FundInfoSql = "UPDATE `fund_info` SET {$fundInfoField} WHERE FundCode = '{$val['FundCode']}'";
                try {
                    $db_local->createCommand($FundInfoSql)->execute();
                    parent::commandLog("更新fund_info成功 sql:{$FundInfoSql}",0,$logFile);
                } catch (Exception $e) {
                    parent::commandLog("更新fund_info失败 sql:{$FundInfoSql}--原因:{$e->getMessage()}",1,$logFile);
                }
                $n++;
            }
            $loginfo = '共更新'.$n.'条数据 耗时'.(time()-$t1).' s';
            parent::commandLog($loginfo,0,$logFile);
        }
        //更新缓存
        $fundArrNew = $db_local->createCommand("SELECT * FROM `fund_info` WHERE FundTypeCode = '1109' OR FundTypeCode='1106'")->queryAll();
        if (!empty($fundArrNew))
        {
            foreach ($fundArrNew as $val)
            {
                $redis->hset('fund_info',$val['FundCode'],json_encode($val,JSON_UNESCAPED_UNICODE));
            }
        }
    }
}