<?php
namespace console\commands;

use Yii;
use common\lib\HundSun;
use common\models\HandleErr;
use Exception;
use common\lib\CommFun;

/**
 * 脚本作用：通过脚本导入在柜台交易下的单，
 * 导入到本地数据库当中供在web上面查看
 *
 * Class TraderecordController
 * @package console\commands
 */
class TraderecordController extends Controller
{
    public $db;

    public function init(){
        $this->db = Yii::$app->db_local;
    }

    //把企业号1000的下属用户交易记录全部导进系统
    public function actionExport()
    {
        $instId = 1000;
        $org_users = $this->db->createCommand("SELECT * FROM `user` where `Instid` = '{$instId}'")->queryAll();
        foreach ($org_users as $key=>$user) {
            $this->actionExportrecord($instId, $user['id']);
        }
    }

    //开始导入记录
    public function actionExportrecord($instId, $uid)
    {
        $trade_order_table = "trade_order_".$instId;
        $date = date("Y-m-d H:i:s");
        $pubParams = null;
        if ($instId == 1000) { //如果等于企业用户
            $pubParams['usertype'] = 'o';
        }
        $hundsun = new HundSun($uid, $pubParams);
        $resS004 = $hundsun->apiRequest('S004', ['pageno'=>1, 'applyrecordno'=>100000]);
        $logFile = 'Confirmback_'.date('Ymd').'.log';
        if ($resS004['code'] == HundSun::SUCC_CODE && !empty($resS004['returnlist'][0])){
            foreach ($resS004['returnlist'] as $key=>$info) {
                $sql = "SELECT * FROM {$trade_order_table} WHERE ApplySerial='{$info['applyserial']}'";
                $is_exists = $this->db->createCommand($sql)->queryOne();
                if (!empty($is_exists)) {
                    continue;
                }
                $callingcode = $info['callingcode'];
                if ($callingcode == 120 || $callingcode = 122) { //申购，认购当申购处理
                    $tradeType = 0;
                } else if ($callingcode == 124) {  //赎回
                    $tradeType = 1;
                } else { //其他不处理
                    continue;
                }
                $tradeDate = date("Y-m-d", strtotime($info['applydate']));
                $orderno = CommFun::getOrderNo($date);
                $add_trade_sql = "insert into {$trade_order_table} set Orderno='{$orderno}',Uid='{$uid}',FundCode='{$info['fundcode']}',";
                $add_trade_sql .= "ApplyShare='{$info['requestshares']}',ApplyAmount='{$info['requestbalance']}',";
                $add_trade_sql .= "ConfirmAmount='{$info['tradeconfirmsum']}',ConfirmShare='{$info['tradeconfirmshare']}',";
                $add_trade_sql .= "ConfirmNetValue='{$info['netvalue']}',Poundage='{$info['poundage']}',AgentSum='{$info['agentsum']}',CallingCode='{$callingcode}',TradeAcco='{$info['tradeacco']}',TradeType='{$tradeType}',";
                $add_trade_sql .= "TradeStatus='{$info['confirmflag']}',HandleStatus='1',OtherInfo='柜台导入',ApplySerial='{$info['applyserial']}',DeductMoney='2',TradeDay='{$tradeDate}',";
                $applydate = date("Y-m-d H:i:s", strtotime($info['applydate']));
                $confirmdate = date("Y-m-d H:i:s", strtotime($info['confirmdate']));

                $add_trade_sql .= "ApplyTime='{$applydate}',ConfirmTime='{$confirmdate}',HandleTime='{$date}'";
                try {
                    $r2 = $this->db->createCommand($add_trade_sql)->execute();
                    $r2 = (int)$r2;
                    parent::commandLog($add_trade_sql.",执行结果：".$r2, 0, $logFile);
                } catch (Exception $e) {
                    parent::commandLog($e->getMessage(), 1, $logFile);
                }
            }
        }
    }
}