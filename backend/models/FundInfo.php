<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Fund Info model.
*/
class FundInfo extends BaseModel
{
    public $CustodyFee;
    public $FundCode;
    public $FundType;
    public $FundName;
    public $ChiSpelling;
    public $PernetValue;
    public $NVDailyGrowthRate;
    public $RRInSingleWeek;
    public $RRInSingleMonth;
    public $RRInThreeMonth;
    public $RRInSixMonth;
    public $RRInSingleYear;
    public $RRSinceThisYear;
    public $DailyProfit;
    public $LatestWeeklyYield;
    public $FundRiskLevel;
    public $FundState;
    public $ShareType;
    public $DeclareState;
    public $SubScribeState;
    public $ValuagrState;
    public $WithDrawState;
    public $MinHoldShare;
    public $MinRedemeShare;
    public $MinPurchaseAmount;
    public $MinSubscribAmount;
    public $MinAddPurchaseAmount;
    public $MinValuagrAmount;
    public $MinAddValuagrAmount;
    public $ManageFee;
    public $MoneyFund;

    public $page;
    public $pageSize;

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
    public static function getTable()
    {
        return 'fund_info';
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
            ['FundCode','required','on'=>['update','search']],
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],

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
            'default' => ['FundCode','CustodyFee','page', 'pageSize'],
            'update' => ['FundCode','CustodyFee','FundType','FundName','ChiSpelling','PernetValue','NVDailyGrowthRate','RRInSingleWeek','RRInSingleMonth','RRInThreeMonth','RRInSixMonth','RRInSingleYear','RRSinceThisYear','DailyProfit','LatestWeeklyYield','FundRiskLevel','FundState','ShareType','DeclareState','SubScribeState','ValuagrState','WithDrawState','MinHoldShare','MinRedemeShare','MinPurchaseAmount','MinSubscribAmount','MinAddPurchaseAmount','MinValuagrAmount','MinAddValuagrAmount','ManageFee','MoneyFund'],
            'search' => ['FundCode','CustodyFee']
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'FundCode') {
            $this->_errors = CommFun::renderFormat('202',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }

    /**
     * 获取统计数据
     */
    public function getStatisticalData($value='')
    {
        $tableName = self::getTable();

        $data = [];
        $data['totalRecords'] = (new Query)->from($tableName)->count();
        $data['hasMaintainCustodyFeeNums'] = (new Query)->from($tableName)->where('CustodyFee>0')->count();
        $data['notMaintainCustodyFeeNums'] = (new Query)->from($tableName)->where('CustodyFee=0')->count();

        return $data;
    }

    /**
     * 可购买基金列表
     */
    public function fundInfo()
    {
        $tableName = self::getTable();

        $sql = " FROM `{$tableName}` WHERE FundCode IS NOT NULL";

        // 刷选条件
        if ($this->CustodyFee) {
            if ($this->CustodyFee == '1') {
                $sql .= " AND CustodyFee > 0";
            }elseif ($this->CustodyFee == '2') {
                $sql .= " AND CustodyFee = 0";
            }
        }

        // 自定义搜索条件
        if ($this->FundCode) {
            $sql .= " AND FundCode LIKE '{$this->FundCode}%'";
        }

        // 计算满足查询条件的总记录数（得在分页sql前）
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY SysTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // 查询交易列表数据
        $sqlTrade = "SELECT *".$sql;
        $command = self::getDb()->createCommand($sqlTrade);
        $data = $command->queryAll();

        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['CustodyFee'] > 0) {
                    $value['CustodyFeeName'] = '已维护';
                }else{
                    $value['CustodyFeeName'] = '未维护';
                }
            }

            // 列表分页附加数据
            $data['totalRecords'] = $totalRecords;   // 先计算总记录数，避免后面添加的字段修改列表元素个数
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }

    /**
     * 更新基金
     */
    public function update()
    {
        $tableName = self::getTable();
        
        // UPDATE
        $command = self::getDb()->createCommand();
        $rs = $command->update($tableName, [
            'CustodyFee' => $this->CustodyFee,
            'FundCode' => $this->FundCode,
            'FundType' => $this->FundType,
            'FundName' => $this->FundName,
            'ChiSpelling' => $this->ChiSpelling,
            'PernetValue' => $this->PernetValue,
            'NVDailyGrowthRate' => $this->NVDailyGrowthRate,
            'RRInSingleWeek' => $this->RRInSingleWeek,
            'RRInSingleMonth' => $this->RRInSingleMonth,
            'RRInThreeMonth' => $this->RRInThreeMonth,
            'RRInSixMonth' => $this->RRInSixMonth,
            'RRInSingleYear' => $this->RRInSingleYear,
            'RRSinceThisYear' => $this->RRSinceThisYear,
            'DailyProfit' => $this->DailyProfit,
            'LatestWeeklyYield' => $this->LatestWeeklyYield,
            'FundRiskLevel' => $this->FundRiskLevel,
            'FundState' => $this->FundState,
            'ShareType' => $this->ShareType,
            'DeclareState' => $this->DeclareState,
            'SubScribeState' => $this->SubScribeState,
            'ValuagrState' => $this->ValuagrState,
            'WithDrawState' => $this->WithDrawState,
            'MinHoldShare' => $this->MinHoldShare,
            'MinRedemeShare' => $this->MinRedemeShare,
            'MinPurchaseAmount' => $this->MinPurchaseAmount,
            'MinSubscribAmount' => $this->MinSubscribAmount,
            'MinAddPurchaseAmount' => $this->MinAddPurchaseAmount,
            'MinValuagrAmount' => $this->MinValuagrAmount,
            'MinAddValuagrAmount' => $this->MinAddValuagrAmount,
            'ManageFee' => $this->ManageFee,
            'MoneyFund' => $this->MoneyFund,
        ],['FundCode' => $this->FundCode])->execute();
        
        if ($rs) {
            Yii::$app->redis->executeCommand('HDEL',['fund_info', $this->FundCode]);   // Delete redis cache
        }
        
        return $rs;
    }
}
