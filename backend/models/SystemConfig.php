<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* News infomation model.
*/
class SystemConfig extends BaseModel
{
    public $id;
    public $title;
    public $keywords;
    public $descriptions;


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
     * @return string|false.
     */
    public static function getTable()
    {
        return 'system_config';
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
            [['title'],'required'],
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
            'default' => ['index'],
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'id' || $attribute == 'category') {
            $this->_errors = CommFun::renderFormat('204',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }
    public function queryConfig($condition=[],$tableName)
    {
        $rs = (new Query)
            ->from($tableName)
            ->where($condition)
            ->one();
        return $rs;
    }

    /**
     * Add news
     * @return [int]
     */
    public function updateConfig($data)
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable();

        $data = ['Title' => htmlspecialchars($data['title']),
            'Keywords' => htmlspecialchars($data['keywords']),
            'Descriptions' => htmlspecialchars($data['descriptions'])];
        if ($this->queryConfig(['id'=>1],$tableName)) { //更新
            $rs = self::getDb()->createCommand()->update($tableName, $data, ['id'=>1])->execute();
        } else {
            // INSERT
            $data['id'] = 1;
            $rs = self::getDb()->createCommand()->insert($tableName, $data)->execute();
        }
        return $rs;
    }
}
