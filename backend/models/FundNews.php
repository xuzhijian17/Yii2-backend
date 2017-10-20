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
class FundNews extends BaseModel
{
    public $cid;
    public $id;
    public $title;
    public $keywords;
    public $descriptions;
    public $link;
    public $content;
    public $excerpt;
    public $feature_image;
    public $online;
    public $recommend;
    public $status;

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
        return 'fund_news_'.$instid;
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
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
            [['title'],'required'],
            ['id','required','on'=>['editNews','delNews','recommendNews']],
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
            'default' => ['cid','page','pageSize'],
            'editNews' => ['id','title','keywords','descriptions','link','content','excerpt','feature_image','online','recommend'],
            'addNews' => ['cid','title','keywords','descriptions','link','content','excerpt','feature_image','online','recommend'],
            'delNews' => ['id'],
            'recommendNews' => ['id','recommend'],
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
     * Verify news whether there exist
     * @return [boolean]
     */
    public function news_exist($condition=[],$tableName)
    {
        $rs = (new Query)
            ->from($tableName)
            ->where($condition)
            ->count()
        ;

        return $rs ? true : false;
    }

    /**
     * News list
     * @return [array]
     */
    public function newsList($value='')
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // 初始sql
        $sql = " FROM {$tableName} fn LEFT JOIN fund_category_news fcn ON fn.CategoryId=fcn.id AND fcn.`Status`=0 WHERE fn.`Status`=0 AND fcn.id={$this->cid} ";

        // 计算满足查询条件的总记录数（得在分页sql前）
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY fn.`Order` DESC, fn.UpdateTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // 查询列表数据
        $sqlList = "SELECT *,fn.id,fn.UpdateTime,fn.InsertTime".$sql;
        $command = self::getDb()->createCommand($sqlList);
        $data = $command->queryAll();

        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['Recommend'] == '1') {
                    $value['RecommendName'] = '已推荐';
                }else{
                    $value['RecommendName'] = '未推荐';
                }
                $value['Title'] = strip_tags($value['Title']);
            }

            // 列表分页附加数据
            $data['totalRecords'] = $totalRecords; 
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }


        return $data;
    }

    /**
     * Add news
     * @return [int]
     */
    public function addNews()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // Verify category whether there exist
        if ($this->news_exist(['title'=>$this->title,'Status'=>0],$tableName)) {
            return CommFun::renderFormat('207');
        }

        // INSERT
        $rs = self::getDb()->createCommand()->insert($tableName, [
            'CategoryId' => $this->cid,
            'Title' => htmlspecialchars($this->title),
            'Keywords' => htmlspecialchars($this->keywords),
            'Descriptions' => htmlspecialchars($this->descriptions),
            'Link' => $this->link,
            'Content' => $this->content,
        ])->execute();
        
        return $rs;
    }

    /**
     * Edit news
     */
    public function editNews()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // Verify news whether there exist
        if (!$this->news_exist(['id'=>$this->id,'Status'=>0],$tableName)) {
            return CommFun::renderFormat('208');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Title' => htmlspecialchars($this->title),
            'Keywords' => htmlspecialchars($this->keywords),
            'Descriptions' => htmlspecialchars($this->descriptions),
            'Link' => $this->link,
            'Content' => $this->content,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $this->id])->execute();

        
        return $rs;
    }

    /**
     * Delete news
     */
    public function DelNews()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // Verify news whether there exist
        if (!$this->news_exist(['id'=>$this->id,'Status'=>0],$tableName)) {
            return CommFun::renderFormat('208');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Status' => -1,
        ],['id' => $this->id])->execute();

        return $rs;
    }

    /**
     * Recommend news
     */
    public function recommendNews()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);
        
        // Verify news whether there exist
        if (!$this->news_exist(['id'=>$this->id,'Status'=>0],$tableName)) {
            return CommFun::renderFormat('208');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Recommend' => $this->recommend,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $this->id])->execute();
        
        return $rs;
    }
}
