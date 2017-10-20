<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;

/**
 * 组合配置模型类
 *
 * Class PortfolioConfig
 * @package frontend\modules\api\models
 */
class PortfolioConfig extends ActiveRecord
{
	public static function tableName()
    {
        return 'portfolio_config';
    }

    /*
     *  获取所有后台组合配置列表
     * @param int $instid   B端商户id
     * @return array
     */
    public static function getPortfolioList($instid = 0)
    {
        $db_local = Yii::$app->db_local;
        $tablename = self::tableName();
        $sql = "SELECT * FROM {$tablename} WHERE (Instid='{$instid}' OR Instid=0) AND Status=1";

        $folio_list = $db_local->createCommand($sql)->queryAll();
        foreach ($folio_list as $key=>$val) {
            $fund_list = json_decode($val['FundList'], true);
            $folio_list[$key]['FundList'] = $fund_list;
            $folio_list[$key]['MinSum'] = self::getPortfolioMinSum($fund_list);
        }
        return $folio_list;
    }

    /*
     * 计算组合最小起购金额
     * @param array $fund_list
     * @return int
     */
    public static function getPortfolioMinSum($fund_list)
    {
        if (empty($fund_list)) {
            return 0;
        }
        $port_min = [];
        foreach($fund_list as $k=>$v) {
            $fund_info = CommFun::GetFundInfo($v['fundcode']);
            if (empty($fund_info)) {
                continue;
            }
            $max_min = max($fund_info['MinPurchaseAmount'], $fund_info['MinRedemeShare']*$fund_info['PernetValue']*1.1, $fund_info['MinHoldShare']*$fund_info['PernetValue']*1.1);
            $fund_min = $max_min / ($v['ratio']/100);
            $port_min[] = $fund_min;
        }
        return max($port_min);
    }

    /*
     * 通过组合id获取组合详情信息
     * @param $portfolioId
     * @return bool
     */
    public static function getPortfolioById($portfolioId)
    {
        if (empty($portfolioId)) {
            return false;
        }
        $db_local = Yii::$app->db_local;
        $tablename = self::tableName();
        $sql = "SELECT * FROM {$tablename} WHERE PortfolioId='{$portfolioId}'";
        $folio = $db_local->createCommand($sql)->queryOne();
        if (!empty($folio)) {
            $fund_list = json_decode($folio['FundList'], true);
            $folio['FundList'] = $fund_list;
            $folio['MinSum'] = self::getPortfolioMinSum($fund_list);
        }
        return $folio;
    }
}
