<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Trade model.
*/
class Trade extends BaseModel
{
    public $id;
    public $uid;
    public $instid;
    public $type;
    public $tradeType;
    public $tradeStatus;
    public $startDate;
    public $endDate;
    public $name;
    public $phone;
    public $card;
    public $page;
    public $pageSize;
    public $pic;
    public $tradeAcco;

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
        return 'trade_order_'.$instid;
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
            // default scenario validate rule.
            ['instid','default','value'=>Yii::$app->admin->instid],
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
            ['id','required'],
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
            'default' => ['uid','instid','type','tradeType','tradeStatus','startDate','endDate','name','phone','card','page', 'pageSize'],
            'btradeRemit'=>['uid','instid','type','tradeType','tradeAcco','startDate','endDate','page', 'pageSize'],
            'detailRemit'=>['id'],
            'uploadRemit'=>['id','pic'],
            'ensureRemit'=>['id']
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

    /**
     * User trade list
     * @return [array]
     */
    public function tradeList()
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM `{$tableName}` tod INNER JOIN `user` u ON tod.Uid=u.id LEFT JOIN user_bank ub ON tod.Uid=ub.Uid LEFT JOIN bank_info bi ON ub.BankSerial=bi.BankSerial LEFT JOIN fund_info fm ON tod.FundCode=fm.FundCode LEFT JOIN partner p ON u.Instid=p.Instid WHERE tod.Uid IS NOT NULL";

        // Custom filter condition
        if (isset($this->tradeStatus) && $this->tradeStatus !== '') {   // Because the trade type value parhaps is '0', so it can't use `empty()` and need to `isset()` replace.
            if ($this->tradeStatus == '1') {
                $sql .= " AND tod.TradeStatus IN (1,2,3)";
            }else{
                $sql .= " AND tod.TradeStatus={$this->tradeStatus}";
            }
        }
        isset($this->tradeType) && $this->tradeType !== '' && $sql .= " AND tod.TradeType={$this->tradeType}"; 
        !empty($this->startDate) && $sql .= " AND UNIX_TIMESTAMP(tod.ApplyTime) >= UNIX_TIMESTAMP('{$this->startDate} 00:00:00')";
        !empty($this->endDate) && $sql .= " AND UNIX_TIMESTAMP(tod.ApplyTime) <= UNIX_TIMESTAMP('{$this->endDate} 23:59:59')";
        isset($this->type) && $this->type !== '' && $sql .= " AND tod.MoneyFund={$this->type}";     // 0:common trade, 1:money trade

        // Custom search condition
        !empty($this->uid) && $sql .= " AND tod.Uid={$this->uid}";
        !empty($this->name) && $sql .= " AND Name LIKE '{$this->name}%'";
        !empty($this->phone) && $sql .= " AND RegPhone LIKE '{$this->phone}%'";
        !empty($this->card) && $sql .= " AND CardID LIKE '{$this->card}%'";
        
        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY tod.ApplyTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $sqlTrade = "SELECT *,tod.id AS id,u.id AS Uid".$sql;
        $command = self::getDb()->createCommand($sqlTrade);
        $data = $command->queryAll();
        
        if ($data) {
            foreach ($data as $key => &$value) {
                $value['Instid'] = $this->instid;

                // Format trade type
                if ($value['TradeType'] === '0') {
                    $value['TradeTypeName'] = '申购';
                }elseif ($value['TradeType'] === '1') {
                    $value['TradeTypeName'] = '赎回';
                }elseif ($value['TradeType'] === '2') {
                    $value['TradeTypeName'] = '撤单';
                }elseif ($value['TradeType'] === '3') {
                    $value['TradeTypeName'] = '定投';
                }else{
                    $value['TradeTypeName'] = '';
                }

                // Format trade status
                if ($value['TradeStatus'] == '9' && $value['TradeType'] == '0') {
                    $value['TradeStatusName'] = '申购中';
                }elseif ($value['TradeStatus'] == '9' && $value['TradeType'] == '1') {
                    $value['TradeStatusName'] = '赎回中';
                }elseif ($value['TradeStatus'] === '4') {
                    $value['TradeStatusName'] = '撤单';
                }elseif (in_array($value['TradeStatus'], ['1','2','3'])) {
                    $value['TradeStatusName'] = '成功';
                }else{
                    $value['TradeStatusName'] = '失败';
                }
            }

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }

    /**
     * Business trade list
     * @return [array]
     */
    public function bTradeList()
    {
        $this->instid = 1000;

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM `{$tableName}` tod INNER JOIN fund_info fm ON tod.FundCode=fm.FundCode LEFT JOIN trade_order_1000_attach toa ON tod.id=toa.TradeOrderId WHERE tod.Uid IS NOT NULL";

        // Custom search condition
        !empty($this->tradeAcco) && $sql .= " AND tod.TradeAcco={$this->tradeAcco}";
        isset($this->tradeType) && $this->tradeType !== '' && $sql .= " AND tod.TradeType={$this->tradeType}"; 
        !empty($this->startDate) && $sql .= " AND UNIX_TIMESTAMP(tod.ApplyTime) >= UNIX_TIMESTAMP('{$this->startDate} 00:00:00')";
        !empty($this->endDate) && $sql .= " AND UNIX_TIMESTAMP(tod.ApplyTime) <= UNIX_TIMESTAMP('{$this->endDate} 23:59:59')";        
        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY tod.ApplyTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $sql = "SELECT * ".$sql;
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryAll();
        
        if ($data) {
            foreach ($data as $key => &$value) {

                // Format trade type
                if ($value['TradeType'] === '0') {
                    $value['TradeTypeName'] = '申购';
                }elseif ($value['TradeType'] === '1') {
                    $value['TradeTypeName'] = '赎回';
                }elseif ($value['TradeType'] === '2') {
                    $value['TradeTypeName'] = '撤单';
                }elseif ($value['TradeType'] === '3') {
                    $value['TradeTypeName'] = '定投';
                }else{
                    $value['TradeTypeName'] = '';
                }

                // Format trade status
                if ($value['TradeStatus'] == '9' && $value['TradeType'] == '0') {
                    $value['TradeStatusName'] = '申购中';
                }elseif ($value['TradeStatus'] == '9' && $value['TradeType'] == '1') {
                    $value['TradeStatusName'] = '赎回中';
                }elseif ($value['TradeStatus'] === '4') {
                    $value['TradeStatusName'] = '撤单';
                }elseif (in_array($value['TradeStatus'], ['1','2','3'])) {
                    $value['TradeStatusName'] = '成功';
                }else{
                    $value['TradeStatusName'] = '失败';
                }

                // Remit status
                if ($value['DeductMoney'] == '2') {
                    $value['DeductMoneyName'] = '已汇款';
                }else{
                    if ($value['TradeType'] === '0') {
                        $value['DeductMoneyName'] = '未汇款';
                    }else{
                        $value['DeductMoneyName'] = '-';
                    }
                }
            }

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }

    /**
     * Upload Remit image
     * @return [array]
     */
    public function uploadRemit()
    {
        $tableName = 'trade_order_1000_attach';
        
        // User data is exist
        if (!(new Query)->from($tableName)->where(['TradeOrderId'=>$this->id])->one()) {
            $sql = "INSERT INTO {$tableName}(TradeOrderId,Pic) VALUES(:id,:pic)";
        }else{
            $sql = "UPDATE {$tableName} SET Pic=:pic WHERE TradeOrderId=:id";
        }

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':pic', $this->pic);
        $command->bindParam(':id', $this->id);
        $rs = $command->execute();
        
        return $rs;
    }

    /**
     * Ensure Remit list
     * @return [array]
     */
    public function ensureRemit()
    {
        $this->instid = 1000;

        $tableName = self::getTable($this->instid);
        
        // User data is exist
        if (!(new Query)->from($tableName)->where(['id'=>$this->id])->one()) {
            return CommFun::renderFormat('110');
        }

        $sql = "UPDATE {$tableName} SET DeductMoney=2 WHERE id=:id";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':id', $this->id);
        $rs = $command->execute();
        
        return $rs;
    }
}
