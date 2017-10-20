<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* User model.
*/
class User extends BaseModel
{
    public $instid;
    public $name;
    public $phone;
    public $card;
    public $openStatus;

    public $uid;
    public $status;
    public $type;

    public $authorization;

    public $CompanyName;
    public $BusinessLicence;
    public $FundAcco;
    public $TradeAcco;
    public $TradePass;
    public $ArtificialPerson;
    public $ArtificialPersonCard;
    public $legalPersonCard;
    public $OperatorName;
    public $OperatorCard;
    public $BankAcco;
    public $BankSerial;
    public $BankName;

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
    public static function getTable($instid='')
    {
        return 'user';
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
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
            // userDetail&userBank&userChangeBank scenario validate rule.
            ['uid', 'required','on'=>['userDetail','userBank','userChangeBank']],
            // modifyAccountStatus scenario validate rule.
            [['uid','status'], 'required','on'=>'modifyAccountStatus'],
            ['status','number','on'=>'modifyAccountStatus'],
            // modifyOpenStatus scenario validate rule.
            [['uid','status'], 'required','on'=>'modifyOpenStatus'],
            ['status','compare','compareValue'=>-2,'operator'=>'==','on'=>'modifyOpenStatus'],
            // modifyAuthorization scenario validate rule.
            [['uid', 'authorization'], 'required','on'=>'modifyAuthorization'],
            ['authorization','number','on'=>'modifyAuthorization'],
            // addBaccount scenario validate rule.
            [['CompanyName','BusinessLicence','TradeAcco', 'TradePass','BankName','BankAcco'], 'required','on'=>['addBaccount','editBaccount']],
            ['uid', 'required','on'=>'editBaccount'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scen   ario.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        return [
            'default' => ['instid','openStatus','name','phone','card','page','pageSize'],
            'userDetail' => ['uid', 'instid'],
            'userBank' => ['uid', 'instid'],
            'userChangeBank' => ['uid', 'instid'],
            'modifyAccountStatus' => ['uid', 'status'],
            'modifyOpenStatus' => ['uid', 'status','type'],
            'modifyAuthorization' => ['uid', 'authorization'],
            'addBaccount' => ['CompanyName', 'BusinessLicence','FundAcco','TradeAcco','TradePass','ArtificialPerson','ArtificialPersonCard','legalPersonCard','OperatorName','OperatorCard','BankAcco','BankSerial','BankName'],
            'editBaccount' => ['uid', 'CompanyName', 'BusinessLicence','FundAcco','TradeAcco','TradePass','ArtificialPerson','ArtificialPersonCard','legalPersonCard','OperatorName','OperatorCard','BankAcco','BankSerial','BankName'],
        ];
    }

    /**
     * Get register number
     *
     * @return Number of records
     */
    public static function getRegisterNum($instid='')
    {
        if ($instid) {
            $condition = ['Instid'=>$instid];
        }else{
            $condition = [];
        }

        return (new Query)->from(self::getTable())->where($condition)->count();
    }

    /**
     * Get bind number
     *
     * @return Number of records
     */
    public static function getBindBankNum($instid='')
    {
        $condition = ['OpenStatus'=>1];
        if ($instid !== '') {
            $condition['Instid'] = $instid;
        }

        return (new Query)->from(self::getTable())->where($condition)->count();
    }

    /**
     * Get user list
     *
     * @return [array] 
     */
    public function userList()
    {
        $tableName = self::getTable();

        // Init sql
        $sql = " FROM `{$tableName}` u INNER JOIN `partner` p ON u.Instid=p.Instid WHERE u.OpenStatus IN (0,1) AND u.Instid!=1000";
        
        // Special permission judgment for superadmin
        if (Yii::$app->admin->isSuperAdmin) {
            if (isset($this->instid) && $this->instid !== '') {     // The instid value maybe is '0'
                $sql .= " AND u.Instid=".$this->instid;
            }
        }else{
            $sql .= " AND u.Instid=".Yii::$app->admin->instid;
        }

        // Filter user bind bank status.PS: if the user is open account, then it's bind bank.
        if (isset($this->openStatus) && $this->openStatus !== '') {     // The openStatus value maybe is '0'
            if ($this->openStatus == 1) {
                $sql .= " AND OpenStatus=1";
            }else{
                $sql .= " AND OpenStatus=0";
            }
        }

        // Custom search condition
        !empty($this->name) && $sql .= " AND Name LIKE '{$this->name}%'";
        !empty($this->phone) && $sql .= " AND RegPhone LIKE '{$this->phone}%'";
        !empty($this->card) && $sql .= " AND CardID LIKE '{$this->card}%'";

        // Get paging total records(before paging limit)
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // Order and Paging
        $sql .= " ORDER BY u.BindTime DESC,u.SysTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $sql = "SELECT *,u.id,u.SysTime".$sql;
        $command = Yii::$app->db->createCommand($sql);
        $data = $command->queryAll();
        
        if ($data) {
            foreach ($data as $key => &$value) {
                // Format data fields
                $value['SysTime'] = date("Y-m-d",strtotime($value['SysTime']));
                $value['BindTime'] = date("Y-m-d",strtotime($value['BindTime']));
            }

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }


    /**
     * Get user detail
     *
     * @return [array]
     */
    public function userDetail()
    {
        $addSql = '';

        $tableName = self::getTable();

        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $addSql .= " AND Instid=".Yii::$app->admin->instid;
        }

        $sql = "SELECT * FROM `{$tableName}` WHERE OpenStatus IN (0,1) AND id=:id".$addSql;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':id', $this->uid);
        return $command->queryOne();
    }

    /**
     * Get user bank info
     *
     * @return [array]
     */
    public function userBank()
    {
        $addSql = '';

        $tableName = self::getTable();

        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $addSql .= " AND u.Instid=".Yii::$app->admin->instid;
        }

        $sql="SELECT *,u.id FROM `{$tableName}` u LEFT JOIN user_bank ub ON u.id=ub.Uid LEFT JOIN bank_info bi ON ub.BankSerial=bi.BankSerial LEFT JOIN partner p ON u.Instid=p.Instid WHERE u.OpenStatus IN (0,1) AND u.id=:id".$addSql;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':id', $this->uid);
        return $command->queryOne();
    }

    /**
     * Get user change bank info
     *
     * @return [array]
     */
    public function userChangeBank()
    {
        $addSql = '';

        $tableName = self::getTable();

        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $addSql .= " AND u.Instid=".Yii::$app->admin->instid;
        }

        $sql="SELECT * FROM `{$tableName}` u RIGHT JOIN change_bankcard_log cbl ON u.id=cbl.Uid WHERE u.OpenStatus IN (0,1) AND u.id=:id".$addSql;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':id', $this->uid);
        $data = $command->queryAll();
        
        return $data;
    }

    /**
     * Freeze/Unfreeze user
     *
     * @return [int] 0|1
     */
    public function modifyAccountStatus()
    {
        $tableName = self::getTable();

        // User data is exist
        if (!(new Query)->from($tableName)->where(['id'=>$this->uid])->one()) {
            return CommFun::renderFormat('106');
        }

        $sql = "UPDATE `{$tableName}` SET AccountStatus=:status WHERE id=:uid";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':uid', $this->uid);
        $command->bindParam(':status', $this->status);
        $rs = $command->execute();
        
        return $rs;
    }

    /**
     * Remove user
     *
     * @return [int] 0|1
     */
    public function modifyOpenStatus()
    {
        $tableName = self::getTable();

        // Make sure the user is not open account
        $openStatus = (new Query)->select('OpenStatus')->from($tableName)->where(['id'=> $this->uid])->scalar();
        if (!$openStatus) {
            return CommFun::renderFormat('106');
        }else{
            if (!$this->type && !in_array($openStatus, [-2,-1,0])) {
                return CommFun::renderFormat('103');
            }
        }

        $sql = "UPDATE `{$tableName}` SET OpenStatus=:status WHERE id=:uid".($this->type ? " AND Instid=1000" :'');
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':uid', $this->uid);
        $command->bindParam(':status', $this->status);
        $rs = $command->execute();
        
        return $rs;
    }

    /**
     * Modify user change bank permission
     *
     * @return [int] 0|1
     */
    public function modifyAuthorization()
    {
        // User data is exist
        if (!(new Query)->from('user_bank')->where(['Uid'=>$this->uid])->one()) {
            return CommFun::renderFormat('106');
        }

        $sql = "UPDATE user_bank SET Authorization=:authorization WHERE Uid=:uid";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':uid', $this->uid);
        $command->bindParam(':authorization', $this->authorization);
        $rs = $command->execute();
        
        return $rs;
    }

    /**
     * Business account list
     *
     * @return [array] 
     */
    public function baccountList()
    {
        $tableName = self::getTable();

        // Init sql
        $sql = " FROM `{$tableName}` u LEFT JOIN `company_attach` ca ON u.id=ca.Uid WHERE u.Instid=1000 AND u.OpenStatus=1";

        // Get paging total records(before paging limit)
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // Order and Paging
        // $sql .= " ORDER BY u.BindTime DESC,u.SysTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;
        $sql .= " ORDER BY u.BindTime DESC,u.SysTime DESC";

        // Select condition
        $sql = "SELECT *,u.id,u.SysTime".$sql;
        $command = Yii::$app->db->createCommand($sql);
        $data = $command->queryAll();
        
        /*if ($data) {
            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }*/
        
        return $data;
    }

    /**
     * Add business account
     */
    public function addBaccount()
    {
        $rs = 0;

        // User data is exist
        $sql = "SELECT * FROM `user` u INNER JOIN company_attach ca ON u.id=ca.Uid WHERE u.OpenStatus=1 AND u.Instid=1000 AND ca.BusinessLicence=:BusinessLicence";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":BusinessLicence",$this->BusinessLicence);
        $isUserExist = $command->queryOne();

        if ($isUserExist) {
            return CommFun::renderFormat('107');
        }

        // the transaction is started on the master connection
        $transaction = self::getDb()->beginTransaction();
        
        try {
            // Add business account
            $command = self::getDb()->createCommand()->insert('user', [
                'Name' => $this->OperatorName,
                'Pass' => CommFun::AutoEncrypt($this->TradePass),
                'LoginPass' => md5($this->TradePass),
                'Instid' => 1000,
                'BindTime' => date("Y-m-d H:i:s"),
                'SysTime' => date("Y-m-d H:i:s")
            ]);
            $rs1 = $command->execute();

            if ($uid = self::getDb()->lastInsertID) {
                // Add business bank
                $command = self::getDb()->createCommand()->insert('user_bank', [
                    'Uid' => $uid,
                    'BankAcco' => $this->BankAcco,
                    'TradeAcco' => $this->TradeAcco,
                    'Capitalmode' => '',
                    'BindTime' => date("Y-m-d H:i:s")
                ]);
                $rs2 = $command->execute();

                // Add business attach
                $command = self::getDb()->createCommand()->insert('company_attach', [
                    'Uid' => $uid,
                    'CompanyName' => $this->CompanyName,
                    'BusinessLicence' => $this->BusinessLicence,
                    'FundAcco' => $this->FundAcco,
                    'TradeAcco' => $this->TradeAcco,
                    'ArtificialPerson' => $this->ArtificialPerson,
                    'ArtificialPersonCard' => $this->ArtificialPersonCard,
                    'OperatorName' => $this->OperatorName,
                    'OperatorCard' => $this->OperatorCard,
                    'BankName' => $this->BankName,
                    'BankAcco' => $this->BankAcco
                ]);
                $rs3 = $command->execute();

                if ($rs1 && $rs2 && $rs3) {
                    $rs = 1;
                }
            }
            
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $rs ? true : false;
    }

    /**
     * Edit business account
     */
    public function editBaccount()
    {
        $rs = 0;

        // the transaction is started on the master connection
        $transaction = self::getDb()->beginTransaction();
        
        try {
            // Update business account
            $command = self::getDb()->createCommand()->update('user', [
                'Name' => $this->OperatorName,
                'Pass' => CommFun::AutoEncrypt($this->TradePass),
            ], ['id'=>$this->uid,'Instid'=>1000]);
            $rs1 = $command->execute();
            /*if (!$rs1) {
                $rs *= $rs1;
            }*/

            // Update business bank
            $command = self::getDb()->createCommand()->update('user_bank', [
                'BankAcco' => $this->BankAcco,
                'TradeAcco' => $this->TradeAcco,
            ], ['Uid'=>$this->uid]);
            $rs2 = $command->execute();
            /*if (!$rs2) {
                $rs *= $rs2;
            }*/

            // Update business attach
            $command = self::getDb()->createCommand()->update('company_attach', [
                'CompanyName' => $this->CompanyName,
                'BusinessLicence' => $this->BusinessLicence,
                'FundAcco' => $this->FundAcco,
                'TradeAcco' => $this->TradeAcco,
                'ArtificialPerson' => $this->ArtificialPerson,
                'ArtificialPersonCard' => $this->ArtificialPersonCard,
                'OperatorName' => $this->OperatorName,
                'OperatorCard' => $this->OperatorCard,
            ], ['Uid'=>$this->uid]);
            $rs3 = $command->execute();
            /*if (!$rs3) {
                $rs *= $rs3;
            }*/

            $rs = 1;
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $rs ? true : false;
    }
}
