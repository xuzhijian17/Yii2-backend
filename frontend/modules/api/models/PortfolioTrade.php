<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;
use yii\debug\models\search\Base;

/**
 * 组合交易记录模型类
 *
 * Class PortfolioTrade
 * @package frontend\modules\api\models
 */
class PortfolioTrade extends BaseModel
{
    //TradeStatus
    const TS_APPLYING = 0; //申请中
    const TS_PART_CONFIRM = 1; //部分确认
    const TS_ALL_CONFIRM = 2; //全部确认

    //HandleStatus
    const HS_UNPROCESS = 0; //未处理
    const HS_PROCESS = 1; //已处理

    //TradeType
    const TT_PURCHASE = 0; //买入
    const TT_SELL = 1; //卖出
    const TT_WITHDRAW = 2; //撤单

    public function __construct($bsNo = 0, $field=[])
    {
        $this->tbName = "portfolio_trade_{$bsNo}";
        parent::__construct($bsNo, $field);
    }

    public  function getPortfolioTradeById($portfolioTradeId)
    {
        if (empty($portfolioTradeId)) {
            return false;
        }
        $sql = /** @lang text */ "SELECT * FROM {$this->tbName} WHERE id='{$portfolioTradeId}'";
        $folio = $this->db->createCommand($sql)->queryOne();
        return $folio;
    }
}
