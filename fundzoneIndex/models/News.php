<?php
namespace fundzone\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\CommFun;

/**
* News model.
*/
class News extends Model
{
    public $fundtype;
	public $company;
    public $fundcode;
    public $fundname;
	public $order;
	public $sort;
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
        return ''.$instid;
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
        ];
    }

    /**
     * Category News
     * @return [array]
     */
    public function getCatNews($value='')
    {
        $tableName = 'fund_category_news';

        $sql ="SELECT *,fc.id,fc.UpdateTime,COUNT(fl.CategoryId) AS Nums FROM {$tableName} fc LEFT JOIN fund_news_0 fl ON fc.id=fl.CategoryId AND fl.`Status`!=-1 WHERE fc.Instid=0 AND fc.`Status`!=-1 GROUP BY fc.id ORDER BY fc.`Order` DESC,fc.UpdateTime DESC";
        $command = self::getDb()->createCommand($sql);
        $rows = $command->queryAll();

        return $rows;
    }

    /**
     * News list
     * @return [array]
     */
    public function getNewsList($cid, $page, $pageSize)
    {
        $tableName = 'fund_news_0';

        // 初始sql
        $sql = " FROM {$tableName} fn LEFT JOIN fund_category_news fcn ON fn.CategoryId=fcn.id AND fcn.`Status`=0 WHERE fn.`Status`=0 AND fcn.id={$cid}";

        // 计算满足查询条件的总记录数（得在分页sql前）
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY fn.`Order` DESC, fn.UpdateTime DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;

        // 查询列表数据
        $sql = "SELECT *,fn.id,fn.UpdateTime,fn.InsertTime".$sql;
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryAll();

        $newsList = ['error'=>0,'list'=>[]];
        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['Recommend'] == '1') {
                    $value['RecommendName'] = '已推荐';
                }else{
                    $value['RecommendName'] = '未推荐';
                }
            }

            $newsList['list'] = $data;
            // 列表分页附加数据
            $newsList['totalRecords'] = $totalRecords; 
            $newsList['totalPages'] = ceil($newsList['totalRecords']/$pageSize); 
            $newsList['page'] = $page;
        }

        return $newsList;
    }

    /**
     * Recommend News list
     * @return [array]
     */
    public function getRecommendNewsList($cid, $limit=3)
    {
        $tableName = 'fund_news_0';

        // 初始sql
        $sql = " FROM {$tableName} fn LEFT JOIN fund_category_news fcn ON fn.CategoryId=fcn.id AND fcn.`Status`=0 WHERE fn.`Status`=0 AND fn.Recommend=1 AND fcn.id={$cid}";

        // 计算满足查询条件的总记录数（得在分页sql前）
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY fn.`Order` DESC, fn.UpdateTime DESC LIMIT ".$limit;

        // 查询列表数据
        $sql = "SELECT *,fn.id,fn.UpdateTime,fn.InsertTime".$sql;
        $command = self::getDb()->createCommand($sql);
        $data = $command->queryAll();

        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['Recommend'] == '1') {
                    $value['RecommendName'] = '已推荐';
                }else{
                    $value['RecommendName'] = '未推荐';
                }
            }
        }

        return $data;
    }

    /**
     * News detail
     * @return 
     */
    public function getNewsDetail($id)
    {
        $tableName = 'fund_news_0';

        $sql ="SELECT * FROM {$tableName} WHERE id=:id AND `Status`=0";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":id",$id);
        $row = $command->queryOne();

        return $row;
    }
}
