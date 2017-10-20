<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Cast Surely model.
*/
class CastSurely extends BaseModel
{
    public $uid;
    public $instid;
    public $type;

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
    public static function getTable($instid)
    {
        return 'valutrade_plan_'.$instid;
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
            // All scenario validate rule.
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
            // CastSurelyAgreement scenario validate rule.
            [['uid','instid'],'required','on'=>'CastSurelyAgreement'],
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
            'default' => [],
            'CastSurelyAgreement' => ['uid','instid','type','page','pageSize'],
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'uid') {
            $this->_errors = CommFun::renderFormat('106',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }

    /**
     * Cast surely agreement
     */
    public function CastSurelyAgreement($value='')
    {
        // Special permission judgment for superadmin
        if (!Yii::$app->admin->isSuperAdmin) {
            $this->instid = Yii::$app->admin->instid;
        }

        $tableName = self::getTable($this->instid);

        // Init sql
        $sql = " FROM {$tableName} vp INNER JOIN `user` u ON vp.Uid=u.id AND vp.Cycleunit=0 INNER JOIN fund_info fi ON fi.FundCode=vp.FundCode WHERE vp.Uid={$this->uid}";

        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();

        // Order and Paging
        $sql .= " ORDER BY vp.SysTime DESC";
        // $sql .= " ORDER BY vp.SysTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;


        // Select condition
        $sql = "SELECT *,vp.id,vp.Uid,vp.SysTime".$sql;
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryAll();

        if ($data) {
            foreach ($data as $key => &$value) {
                // 成功扣款次数
                $value['SuccNum'] = (new Query)->select(['id'])->from('trade_order_'.$this->instid)->where(['Xyh'=>$value['Xyh'],'TradeType'=>3,'TradeStatus'=>[1,2,3]])->count();

                if ($value['State'] == 'A') {
                    $value['StateName'] = '启用';
                }elseif ($value['State'] == 'P') {
                    $value['StateName'] = '暂停';
                }elseif ($value['State'] == 'H') {
                    $value['StateName'] = '终止';
                }else{
                    $value['StateName'] = '';
                }
            }

            // Other additional data fields
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }
}
