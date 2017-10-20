<?php
/**
 * 统计各个渠道‘佣金’，‘保有量’，‘日申购中’，‘日赎回中’，‘注册数’
 *
 * @author Jason<anwen2218@gmail.com>
 */
namespace console\commands;

use Yii;
use console\models\DataStat;
class StatsController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        echo 'this is index';
    }

    /*
     * 注册数量统计，一个小时统计一次
     * '0 *\/1 * * * php /data/selffund/webscript/yii stats/regist'
     * */
    public function actionRegist()
    {
        $t1 = time();
        set_time_limit(0);
        $cids = self::getCids(); 
        foreach($cids as $cid)
        {
            $tmpDs = new DataStat($cid);
            $rsReg = $tmpDs->statsRegByCid();
            unset($tmpDs);
        }
        parent::commandLog("本次统计注册量完成耗时".(time()-$t1).'秒',0,'Regist_'.date('Ymd').'.log');
    }
    /*
     * 日申请额度，日确认额度
     * '0 *\/1 * * * php /data/selffund/webscript/yii stats/regist'
     * */
    public function actionApplyconfirm()
    {
        $t1 = time();
        set_time_limit(0);
        $cids = self::getCids(); 
        foreach($cids as $cid)
        {
            $tmpDs = new DataStat($cid);
            $rsReg = $tmpDs->buySellAmount();
            unset($tmpDs);
        }
        parent::commandLog("本次统计日申请额/确认额完成耗时".(time()-$t1).'秒',0,'Applyconfirm_'.date('Ymd').'.log');
        $this->runAction('regist');
    }

    //统计日保有量和佣金
    public function actionTotalasset()
    {
        $t1 = time();
        set_time_limit(0);
        $cids = self::getCids();
        foreach($cids as $cid)
        {
            $tmpDs = new DataStat($cid);
            $rsReg = $tmpDs->commAssetByChannel();
            unset($tmpDs);
        }
        parent::commandLog("本次统计日保有量/佣金完成耗时".(time()-$t1).'秒',0,'Totalasset_'.date('Ymd').'.log');
    }
}
