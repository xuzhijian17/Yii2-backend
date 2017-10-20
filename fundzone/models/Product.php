<?php
namespace fundzone\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\CommFun;

/**
* Product model.
*/
class Product extends Model
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
			['order','default','value'=>'FundCode'],
			['sort','default','value'=>'DESC'],
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
            'default' => ['fundtype','company','fundcode','fundname','order','sort','page','pageSize'],
        ];
    }

    public function getRecommend()
    {
        $sql = "SELECT * FROM fund_list_0 fl LEFT JOIN fund_info fi ON fl.FundCode=fi.FundCode WHERE fl.Status=0 AND fl.Recommend=1 ORDER BY fl.UpdateTime DESC";
        $command = Yii::$app->db->createCommand($sql);
        return $command->queryAll();
    }

    public function getFundType()
    {
		$sql = "SELECT FundType FROM fund_info GROUP BY FundType";
		$command = Yii::$app->db->createCommand($sql);
        return $command->queryColumn();
    }
	
	public function getFundCompany()
    {
		$sql = "SELECT InvestAdvisorName FROM fund_info GROUP BY InvestAdvisorName HAVING LENGTH(TRIM(InvestAdvisorName))>0";
		$command = Yii::$app->db->createCommand($sql);
        return $command->queryColumn();
    }
		
    /*
    * 基金推荐（首页）
    */
	public function getFundList($data)
    {        
        // 初始sql
        $sql = " FROM fund_list_0 fl LEFT JOIN fund_category fc ON fl.CategoryId=fc.id AND fc.`Status`!=-1 LEFT JOIN fund_info fi ON fl.FundCode=fi.FundCode WHERE fl.`Status`=0";

		isset($data['fundtype']) && $sql .= " AND fc.Category='{$data['fundtype']}'";
		
		$page = isset($data['page']) && (int) $data['page'] > 0 ? $data['page'] : 1;
		$pageSize = isset($data['pageSize']) ? $data['pageSize'] : 15;
		
        // 分页参数
        $sql .= " ORDER BY fl.IsTop DESC, fl.UpdateTime DESC LIMIT 20";

        // 查询列表数据
        $sqlList = "SELECT *,fl.id".$sql;
        $command = self::getDb()->createCommand($sqlList);
        $data = $command->queryAll();
        
        $fundlist = ['error'=>0,'list'=>[]];
        if ($data) {
            foreach ($data as $key => &$value) {
                $value['TradingDay'] = date("Y-m-d",strtotime($value['TradingDay']));
                
                if ($value['Recommend'] == '1') {
                    $value['RecommendName'] = '已推荐';
                }else{
                    $value['RecommendName'] = '未推荐';
                }
            }
            $fundlist['error'] = '0';
            $fundlist['list'] = $data;
        }
        
        return $fundlist;
    }

    /*
    * 基金产品
    */
    public function fundList($data)
    {
        $sql = " FROM fund_info WHERE FundCode IS NOT NULL";

        if(isset($data['fundtype']) && $data['fundtype'] != ''){
            $sql .= " AND FundType='".$data['fundtype']."'";
        }
        if(isset($data['company']) && $data['company'] != ''){
            $sql .= " AND InvestAdvisorName='".$data['company']."'";
        }
        if(isset($data['fundcode']) && $data['fundcode'] != ''){
            $sql .= " AND FundCode='".$data['fundcode']."'";
        }else if(isset($data['fundname']) && $data['fundname'] != ''){
            $sql .= " AND FundName LIKE '".$data['fundname']."%'";
        }
        // console.log(sql);
        $order = isset($data['order']) ? $data['order'] : 'FundCode';
        $sort = isset($data['sort']) ? $data['sort'] : 'DESC';
        $page = isset($data['page']) && (int) $data['page'] > 0 ? $data['page'] : 1;
        $pageSize = isset($data['pageSize']) ? $data['pageSize'] : 15;
        
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        $sql .= " ORDER BY ".$order." ".$sort." LIMIT ".(($page-1)*$pageSize).",".$pageSize;
        
        $command = Yii::$app->db->createCommand("SELECT *".$sql);
        $data = $command->queryAll();
        
        $fundlist = ['error'=>0,'list'=>[]];
        if ($data) {
            foreach ($data as $key => &$value) {
                $value['TradingDay'] = date("Y-m-d",strtotime($value['TradingDay']));
            }
            $fundlist['error'] = '0';
            $fundlist['list'] = $data;
            // Other additional data fields
            $fundlist['totalRecords'] = $totalRecords;
            $fundlist['totalPages'] = ceil($totalRecords/$pageSize); 
            $fundlist['page'] = $page;
        }

        return $fundlist;
    }
}
