<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use backend\models\Position;
use common\lib\CommFun;

class Statistics extends BaseModel
{
    public $instid;
    public $startDate;
    public $endDate;
    public $page;
    public $pageSize;

    public static $sumRegNums;
    public static $sumBindNums;
    public static $sumTotalBuyAmount;
    public static $sumTotalSellAmount;
    public static $sumCommission;

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
    public static function getTable($table_name='')
    {
        return 'statistics_'.$table_name;
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Note, in order to inherit rules defined in the parent class, a child class needs to
     * merge the parent rules with child rules using functions such as `array_merge()`.
     *
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [
            // All scenario validate rule.
            ['instid','default','value'=>Yii::$app->admin->instid],
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        return [
            'default' => ['instid','startDate','endDate','page','pageSize'],
        ];
    }

    /**
     * 总量统计
     */
    public function baseStatistics()
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM {$tableName} WHERE id IS NOT NULL ";

        // Custom filter condition
        !empty($this->startDate) && $sql .= " AND UNIX_TIMESTAMP(Day) >= UNIX_TIMESTAMP('{$this->startDate} 00:00:00')";
        !empty($this->endDate) && $sql .= " AND UNIX_TIMESTAMP(Day) <= UNIX_TIMESTAMP('{$this->endDate} 23:59:59')";

        // Select condition
        $command = Yii::$app->db->createCommand("SELECT *".$sql);
        $data = $command->queryAll();

        $totalStatistics = [];
        if ($data) {
            foreach ($data as $key => $value) {
                // 总量统计
                self::$sumRegNums += $value['RegNums'];
                self::$sumBindNums += $value['BindNums'];
                self::$sumTotalBuyAmount += $value['TotalBuyAmount'];
                self::$sumTotalSellAmount += $value['TotalSellAmount'];
                self::$sumCommission += $value['Commission'];
            }
        }

        $totalStatistics['sumRegNums'] = round(self::$sumRegNums,2);
        $totalStatistics['sumBindNums'] = round(self::$sumBindNums,2);
        $totalStatistics['sumTotalBuyAmount'] = round(self::$sumTotalBuyAmount,2);
        $totalStatistics['sumTotalSellAmount'] = round(self::$sumTotalSellAmount,2);
        $totalStatistics['sumCommission'] = round(self::$sumCommission,2);

        return $totalStatistics;
    }

    /**
     * 每日统计
     */
    public function everydayStatistics()
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM {$tableName} WHERE id IS NOT NULL ";

        // Custom filter condition
        !empty($this->startDate) && $sql .= " AND UNIX_TIMESTAMP(Day) >= UNIX_TIMESTAMP('{$this->startDate} 00:00:00')";
        !empty($this->endDate) && $sql .= " AND UNIX_TIMESTAMP(Day) <= UNIX_TIMESTAMP('{$this->endDate} 23:59:59')";

        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY Day DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $command = Yii::$app->db->createCommand("SELECT *".$sql);
        $data = $command->queryAll();

        if ($data) {
            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }

        return $data;
    }

    /**
     * 商户数据统计
     */
    public function instStatistics($value='')
    {
        # code...
    }

    /**
     * 用户持仓数据统计
     */
    public function userStatistics($params='')
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }
        
        // 初始化sql
        $sql = " FROM `user` u LEFT JOIN `admin` a ON u.Instid=a.id";

        // 自定义搜索条件（是否得建一张用户持仓统计表?）
        // isset($params['assets']) && !empty($params['assets']);

        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY u.BindTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $command = Yii::$app->db->createCommand("SELECT *,u.id as id".$sql);
        $data = $command->queryAll();

        if ($data) {
            // $positionTableName = 'fund_position_'.$instid;
            foreach ($data as $key => &$value) {
                $positionStatistics = self::positionStatistics($value['id'],$instid);    // 用户持仓统计
                $fundInfo = CommFun::GetFundInfo($positionStatistics['FundCode']);  // 用于获取最新净值

                if (!$fundInfo) {
                    continue;
                }

                // $value['PernetValue'] = $fundInfo['PernetValue'];    // 最新净值
                $value['TotalAssets'] = $positionStatistics['SumShare']*$fundInfo['PernetValue'];   // 用户总资产/总市值
                $value['TotalProfitLoss'] = $positionStatistics['SumProfitLoss'];   // 总累计盈亏

                $value['TotalApplyAmount'] = self::tradeStatistics($value['id'],$instid,3)*$fundInfo['PernetValue'];    // 总累计申购金额

                $value['TotalConfirmAmount'] = self::tradeStatistics($value['id'],$instid,4);    // 总累计赎回金额
            }

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }

    /**
     * 用户持仓统计
     */
    public static function positionStatistics($uid='', $tableName)
    {
        $sql = "SELECT FundCode,SUM(CurrentRemainShare) AS SumShare,SUM(FreezeSellShare) AS SumFreezeSellShare,SUM(UnpaidIncome) AS SumUnpaidIncome,SUM(DayProfitLoss) AS SumDayProfitLoss,SUM(TotalProfitLoss) AS SumProfitLoss FROM fund_position_{$tableName}";

        if ($uid) {
            $sql .= " WHERE Uid={$uid}";
        }
        
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryOne();

        return $data;
    }

    /**
     * 用户交易统计，指具体某个商户下的某个用户的所有持仓的统计信息
     * @param [int] $type 1-申购中（未确认金额） 2-赎回中（未确认份额） 3-累计申购（确认份额） 4-累计赎回（确认金额）
     */
    public static function tradeStatistics($uid, $instid, $type)
    {
        if($type == 1){
            // 买入申请，返回申请金额
            $sql = "SELECT SUM(ApplyAmount) AS SumApplyAmount FROM trade_order_{$instid} WHERE Uid={$uid} AND TradeType=0 AND TradeStatus=9";
        }elseif ($type == 2) {
            // 卖出申请，返回份额
            $sql = "SELECT SUM(ApplyShare) AS SumApplyShare FROM trade_order_{$instid} WHERE Uid={$uid} AND TradeType=1 AND TradeStatus=9";
        }elseif ($type == 3) {
            // 买入确认，返回份额
            $sql = "SELECT SUM(ConfirmShare) AS SumConfirmShare FROM trade_order_{$instid} WHERE Uid={$uid} AND TradeType=0 AND TradeStatus IN (1,2,3)";
        }elseif ($type == 4) {
            // 卖出确认，返回金额
            $sql = "SELECT SUM(ConfirmAmount) AS SumConfirmAmount FROM trade_order_{$instid} WHERE Uid={$uid} AND TradeType=1 AND TradeStatus IN (1,2,3)";
        }
        
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryScalar();

        return $data;
    }
}
