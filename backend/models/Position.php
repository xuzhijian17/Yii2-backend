<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use backend\models\Statistics;
use common\lib\CommFun;

/**
* Position model.
*/
class Position extends BaseModel
{
    public $id;
    public $uid;
    public $instid;
    public $type;
    public $page;
    public $pageSize;

    protected static $totalMarketValue;    // 总市值
    protected static $totalBuyApplyAmount;     // 总申购中金额
    protected static $totalSellApplyAmount;   // 总赎回中金额
    protected static $totalBuyConfirmAmount;   // 总累计申购金额（申请并确认）
    protected static $totalSellConfirmAmount;   // 总累计赎回金额（申请并确认）
    protected static $totalProfitLoss;      // 总累计盈亏
    protected static $totalDayProfitLoss;   // 昨/当日累计盈亏
    protected static $totalUnpaidIncome;    // 总未付收益

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns the database connection used by this Model class.
     * By default, the "db" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     * @param [string] db component name.
     * @return Connection the database connection used by this Model class.
     */
    public static function getDb($db_name='')
    {
        return $db_name ? Yii::$app->$db_name : Yii::$app->db;
    }

    /**
     * Returns the trade order table name.
     * @param [mixed] instid.
     * @return string|false.
     */
    public static function getTable($instid)
    {
        return 'fund_position_'.$instid;
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [
            ['page','default','value'=>1,'on'=>['default','profitLoss']],
            ['pageSize','default','value'=>Yii::$app->params['pageSize'],'on'=>['default','profitLoss']],
            // default scenario validate rule.
            [['uid','instid'],'required'],
            // profitLoss scenario validate rule.
            [['id','uid','instid'],'required'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios($value='')
    {
        return [
            'default' => ['uid','instid','type','page', 'pageSize'],
            'profitLoss' => ['id','uid','instid','page', 'pageSize']
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'instid') {
            $this->_errors = CommFun::renderFormat('105',[],$error);
        }elseif($attribute == 'uid'){
            $this->_errors = CommFun::renderFormat('106',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }


    public static function positionStatistics($instid,$uid)
    {
        $tradeTableName = "trade_order_{$instid}";

        self::$totalBuyApplyAmount = (new Query)->from($tradeTableName)->where(['Uid'=>$uid,'TradeType'=>0,'TradeStatus'=>9])->sum('ApplyAmount');    // 申购中金额（买入未确认）
        self::$totalBuyConfirmAmount = (new Query)->from($tradeTableName)->where(['Uid'=>$uid,'TradeType'=>0,'TradeStatus'=>[1,2,3]])->sum('ConfirmAmount');    // 累计申购金额（买入确认）
        self::$totalSellConfirmAmount = (new Query)->from($tradeTableName)->where(['Uid'=>$uid,'TradeType'=>1,'TradeStatus'=>[1,2,3]])->sum('ConfirmAmount');     // 累计赎回金额（卖出确认）
        self::$totalProfitLoss = (new Query)->from(self::getTable($instid))->where(['Uid'=>$uid])->sum('TotalProfitLoss');    // （历史）累计盈亏
    }

    /**
     * User position list
     *
     * @return [array]
     */
    public function positionDetail()
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM {$tableName} fp INNER JOIN `user` u ON fp.Uid=u.id INNER JOIN fund_info fi ON fp.FundCode=fi.FundCode WHERE fp.Uid=:uid ";

        if ($this->type == '1') {
            $sqlPosition = "SELECT *,fp.id,fp.FundCode".$sql;    // 现金宝
        }else{
            $sqlPosition = "SELECT *,fp.id,fp.FundCode".$sql;
        }

        $command = self::getDb()->createCommand($sqlPosition);
        $command->bindParam(':uid', $this->uid);
        $data = $command->queryAll();

        // 资产信息统计
        self::positionStatistics($this->instid,$this->uid);
        
        if ($data) {
            foreach ($data as $key => &$value) {
                $fundInfo = CommFun::GetFundInfo($value['FundCode']);  // 获取最新净值

                if (!$fundInfo) {
                    continue;
                }

                $value['UnitNV'] = $fundInfo['PernetValue'];
                $value['MarketValue'] = round($value['CurrentRemainShare']*$fundInfo['PernetValue'],2);  // 市值
                $value['RedeemableShare'] = $value['CurrentRemainShare']-$value['FreezeSellShare'];    // 可赎回份额 = 当前份额-冻结份额
                $value['FreezeSellAmount'] = $value['FreezeSellShare']; // 赎回中份额

                // 申购中金额（注：持仓中某只基金的所有申购金额）
                $value['ApplyAmount'] = (new Query)->from("trade_order_{$this->instid}")->where(['Uid'=>$this->uid,'TradeType'=>0,'TradeStatus'=>9,'FundCode'=>$value['FundCode']])->sum('ApplyAmount')?:0;
            
                // 计算总市值/资产和总赎回中金额需要有持仓记录才能计算
                self::$totalMarketValue += $value['MarketValue'];   // 计算总市值/总资产
                self::$totalSellApplyAmount += $value['FreezeSellAmount'];  // 计算总赎回中金额
            }

            // 计算盈亏/收益需要有持仓记录才能计算
            self::$totalDayProfitLoss = (new Query)->from($tableName)->where(['Uid'=>$this->uid])->sum('DayProfitLoss');  // 昨日盈亏（昨日总收益）
            self::$totalUnpaidIncome = (new Query)->from($tableName)->where(['Uid'=>$this->uid])->sum('UnpaidIncome');  // 总未付收益
        }

        // position info statistics
        $data['TotalMarketValue'] = round(self::$totalMarketValue,2);   // 总市值
        $data['TotalBuyConfirmAmount'] = round(self::$totalBuyConfirmAmount,2);     // 累计申购金额
        $data['TotalSellConfirmAmount'] = round(self::$totalSellConfirmAmount,2);   // 累计赎回金额
        $data['TotalBuyApplyAmount'] = round(self::$totalBuyApplyAmount,2);     // 总申购中金额
        $data['TotalSellApplyAmount'] = round(self::$totalSellApplyAmount,2);   // 总赎回中金额
        // profit info statistics
        $data['TotalProfitLoss'] = round(self::$totalProfitLoss,2);    // 总收益/累计盈亏
        $data['TotalDayProfitLoss'] = round(self::$totalDayProfitLoss,2);    // 昨日总收益/累计盈亏
        $data['TotalUnpaidIncome'] = round(self::$totalUnpaidIncome,2);    // 总未付收益

        return $data;
    }

    /**
     * Daily profit and loss
     */
    public function profitLoss()
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = "position_profitloss_".$this->instid;

        // Init sql
        $sql = " FROM {$tableName} pp INNER JOIN fund_info fm ON pp.FundCode=fm.FundCode WHERE pp.PositionId={$this->id} AND pp.uid={$this->uid}";

        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY pp.TradeDay DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $command = self::getDb()->createCommand("SELECT *".$sql);
        $data = $command->queryAll();
        
        if ($data) {

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }
}
