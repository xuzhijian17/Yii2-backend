<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use backend\models\BaseModel;
use yii\db\Query;
use common\lib\CommFun;

/**
* Fund Theme model.
*/
class Theme extends BaseModel
{
    public $id;
    public $Theme;
    public $Describe;
    public $Content;
    public $Image;
    public $Status;
    public $Recommend;

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
        return 'fund_theme';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Each rule is an array with the following structure:
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
            ['Theme','required','on'=>'addTheme'],
            [['id','Theme'],'required','on'=>'editTheme'],
            ['id','required','on'=>['editTheme','delTheme','recommendTheme','onlineTheme']],
            ['Status','in','range'=>[0,1],'on'=>'onlineTheme'],
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
            'addTheme' => ['Theme','Describe','Content','Image','Status','Recommend'],
            'editTheme' => ['id','Theme','Describe','Content','Image','Status','Recommend'],
            'delTheme' => ['id'],
            'recommendTheme' => ['id','Recommend'],
            'onlineTheme' => ['id','Status'],
        ];
    }

    /**
     * Verify theme whether there exist
     * @return [boolean]
     */
    public function theme_exist($condition=[],$tableName)
    {
        $rs = (new Query)
            ->from($tableName)
            ->where($condition)
            ->count()
        ;

        return $rs ? true : false;
    }

    /**
     * Theme list
     */
    public function themeList($value='')
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable();

        $sql =" FROM {$tableName} fc LEFT JOIN fund_list_{$instid} fl ON fc.id=fl.ThemeId AND fl.`Status`!=-1 WHERE fc.Instid={$instid} AND fc.`Status`!=-1 GROUP BY fc.id ORDER BY fc.UpdateTime DESC";

        $sqlCat = "SELECT *,fc.id,fc.Status,fc.Recommend,fc.InsertTime,fc.UpdateTime,COUNT(fl.ThemeId) AS FundNums".$sql;
        $command = self::getDb()->createCommand($sqlCat);
        $data = $command->queryAll();

        foreach ($data as $key => &$value) {
            if ($value['Status'] == '1') {
                $value['StateName'] = '已上线';
            }elseif ($value['Status'] == '-1') {
                $value['StateName'] = '已下线';
            }else{
                $value['StateName'] = '未上线';
            }

            if ($value['Recommend'] == '1') {
                $value['RecommendName'] = '推荐';
            }else{
                $value['RecommendName'] = '未推荐';
            }
        }
        
        return $data;
    }

    /**
     * Add theme
     */
    public function addTheme()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable();

        // Verify theme whether there exist
        if ($this->theme_exist(['Theme'=>$this->Theme,'Instid'=>$instid,'Status'=>[0,1]],$tableName)) {
            return CommFun::renderFormat('209');
        }

        // INSERT
        $rs = self::getDb()->createCommand()->insert($tableName, [
            'Instid' => $instid,
            'Theme' => $this->Theme,
            'Describe' => $this->Describe,
            'Content' => $this->Content,
            'Image' => $this->Image?'/files/'.$this->Image:'',
            'ThumbnailImage' => $this->Image?'/files/thumbnail/'.$this->Image:'',
            'Recommend' => 0,
            'Status' => 0
        ])->execute();

        return $rs;
    }

    /**
     * Edit theme
     */
    public function editTheme()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // Verify theme whether there exist
        if (!$this->theme_exist(['id'=>$this->id,'Instid'=>$instid,'Status'=>[0,1]],$tableName)) {
            return CommFun::renderFormat('210');
        }

        // UPDATE
        $data = [
            'Instid' => $instid,
            'Theme' => $this->Theme,
            'Describe' => $this->Describe,
            'Content' => $this->Content,
            'UpdateTime' => date("Y-m-d H:i:s")
        ];

        if ($this->Image) {
            $data['Image'] = '/files/'.$this->Image;
            $data['ThumbnailImage'] = '/files/thumbnail/'.$this->Image;
        }

        $rs = self::getDb()->createCommand()->update($tableName, $data,['id' => $this->id])->execute();

        return $rs;
    }

    /**
     * Delete theme
     */
    public function delTheme()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // Verify theme whether there exist
        if (!$this->theme_exist(['id'=>$this->id,'Instid'=>$instid,'Status'=>[0,1]],$tableName)) {
            return CommFun::renderFormat('210');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Status' => -1,
        ],['id' => $this->id])->execute();

        return $rs;
    }

    /**
     * Recommend theme
     */
    public function recommendTheme()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);
        
        // Verify theme whether there exist
        if (!$this->theme_exist(['id'=>$this->id,'Instid'=>$instid,'Status'=>[0,1]],$tableName)) {
            return CommFun::renderFormat('210');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Recommend' => $this->Recommend,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $this->id])->execute();
        
        return $rs;
    }

    /**
     * Online theme
     */
    public function onlineTheme()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);
        
        // Verify theme whether there exist
        if (!$this->theme_exist(['id'=>$this->id,'Instid'=>$instid,'Status'=>[0,1]],$tableName)) {
            return CommFun::renderFormat('210');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Status' => $this->Status,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $this->id])->execute();
        
        return $rs;
    }
}
