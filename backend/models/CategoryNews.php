<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Fund category model.
*/
class CategoryNews extends BaseModel
{
    public $id;
    public $category;

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
        return 'fund_category_news';
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
            [['id','category'],'required','on'=>'editNewsCat'],
            ['category','required','on'=>'addNewsCat'],
            ['id','required','on'=>'delNewsCat'],
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
            // 'default' => [],
            'editNewsCat' => ['id','category'],
            'addNewsCat' => ['category'],
            'delNewsCat' => ['id'],
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

    /**
     * Verify category whether there exist
     * @return [boolean]
     */
    public function isCategory($condition=[])
    {
        $isCategory = (new Query)
            ->from(self::getTable())
            ->where($condition)
            ->count()
        ;

        return $isCategory ? true : false;
    }

    /**
     * Category list
     * @return [array]
     */
    public function catList($value='')
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable();

        $sql ="SELECT *,fc.id,fc.UpdateTime,COUNT(fl.CategoryId) AS Nums FROM {$tableName} fc LEFT JOIN fund_news_{$instid} fl ON fc.id=fl.CategoryId AND fl.`Status`!=-1 WHERE fc.Instid={$instid} AND fc.`Status`!=-1 GROUP BY fc.id ORDER BY fc.UpdateTime DESC";
        $command = self::getDb()->createCommand($sql);
        $rows = $command->queryAll();

        return $rows;
    }

    /**
     * Add category
     * @return [int]
     */
    public function addNewsCat()
    {
        $instid = Yii::$app->admin->instid;

        // Verify category whether there exist
        if ($this->isCategory(['Category'=>$this->category,'Instid'=>$instid,'Status'=>0])) {
            return CommFun::renderFormat('203');
        }

        // INSERT
        $rs = self::getDb()->createCommand()->insert(self::getTable(), [
            'Instid' => $instid,
            'Category' => $this->category,
        ])->execute();
        
        return $rs;
    }

    /**
     * Edit category
     */
    public function editNewsCat()
    {
        $instid = Yii::$app->admin->instid;

        // Verify category whether there exist
        if (!$this->isCategory(['id'=>$this->id,'Instid'=>$instid,'Status'=>0])) {
            return CommFun::renderFormat('204');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update(self::getTable(), [
            'Category' => $this->category
        ],['id' => $this->id,'Instid' => $instid])->execute();

        
        return $rs;
    }

    /**
     * Delete category
     */
    public function DelNewsCat()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable();

        $rs = 0;

        // the transaction is started on the master connection
        $transaction = self::getDb()->beginTransaction();
        
        try {
            // Delete category
            $sql = "UPDATE {$tableName} SET `Status`=-1 WHERE id=:id AND Instid=:instid";
            $command = self::getDb()->createCommand($sql);
            $command->bindParam(":id",$this->id);
            $command->bindParam(":instid",$instid);
            $rs1 = $command->execute();
            if ($rs1) {
                $rs += 1;
            }

            // Delete fund of category
            $sql = "UPDATE {$tableName} fc LEFT JOIN fund_news_{$instid} fl ON fc.id=fl.CategoryId SET fl.`Status`=-1 WHERE fc.id=:id AND fc.Instid=:instid";
            $command = self::getDb()->createCommand($sql);
            $command->bindParam(":id",$this->id);
            $command->bindParam(":instid",$instid);
            $rs2 = $command->execute();
            if ($rs2) {
                $rs += 1;
            }
            
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $rs ? true : false;
    }
}
