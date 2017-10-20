<?php
namespace console\commands;

use Yii;
use console\models\Valutrade;

/**
 * This command echoes the first argument that you have entered.
 * 工作日1-5，每天10:30执行
 * "30 10 * * 1,2,3,4,5 php /data/selffund/webscript/yii valutrade"
 *
 * @author Jason<anwen2218@gmail.com>
 */
class ValutradeController extends Controller
{
    /**
     * 查询Hundsun系统的定投信息，同步到trade_order_x
     * 
     */
    public function actionIndex()
    {
        $t1 = time();
        set_time_limit(0);
        $n = 0;
        $cids = self::getCids(); 
        foreach($cids as $cid)
        {
            $tmpVt = new Valutrade($cid); 
            $row = $tmpVt->iuOrderPosition();
            $n +=$row;
            unset($tmpVt);
        }
        parent::commandLog("更新{$n}条数据 耗时".(time()-$t1).'秒',0,'Valutrade_'.date('Ymd').'.log');
    }
}
