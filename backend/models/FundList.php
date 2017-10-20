<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Fund List model.
*/
class FundList extends BaseModel
{
    public $id;
    public $type;
    public $page;
    public $pageSize;

    public $fundCode;
    public $fundName;

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
        return 'fund_list_'.$instid;
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
            ['fundCode','required','message'=>'缺少必传参数：fundCode','on'=>'addFundList'],
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
            'default' => ['id','type','page', 'pageSize'],
            'addFundList' => ['id','type','fundCode','fundName']
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'fundCode') {
            $this->error = CommFun::renderFormat('202',[],['message'=>$error,'rewrite'=>true]);
        }elseif($attribute == 'id' || $attribute == 'type'){
            $this->_errors = CommFun::renderFormat('101',[],$error);
        }else{
            $this->errors[$attribute][] = $error;
        }
    }

    /**
    * 获取可购买基金列表（取恒生接口）
    * @return array
    */
    public function getFundInfo($fundCode='')
    {
        /*$oHundSun = new \common\lib\HundSun();
        $rs = $oHundSun->apiRequest('T001');

        if ($rs['code'] !== "ETS-5BP0000") {
            return CommFun::renderFormat('-1000',[],$rs['message']);
            // exit(json_encode(CommFun::renderFormat('-1000',[],$rs['message']),JSON_UNESCAPED_UNICODE));
        }
        
        return isset($rs['items']) ? $rs['items'] : [];*/

        if ($fundCode) {
            $condition = ['FundCode'=>$fundCode];
            $rs = (new Query)->select(['FundCode as fundcode','FundName as fundname','FundType as fundtype'])->from('fund_info')->where($condition)->one(self::getDb());
        }else{
            $condition = [];
            $rs = (new Query)->select(['FundCode as fundcode','FundName as fundname','FundType as fundtype'])->from('fund_info')->where($condition)->all(self::getDb());
        }

        return $rs;
    }

    /**
     * 获取基金列表
     * @param $id Type id，maybe is ThemeId or CategoryId, according to type field.
     * @param $type 1-theme 2-fundtype 3-hot
     */
    public function fundList()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);
        
        // 初始sql
        $sql = " FROM fund_list_{$instid} fl ";

        if ($this->type == \backend\controllers\BusinessController::THEMETYPE) {
            $sql .= "LEFT JOIN fund_theme ft ON fl.ThemeId=ft.id AND fl.`Status`!=-1 WHERE fl.`Status`=0 AND ft.id={$this->id}";
        }elseif ($this->type == \backend\controllers\BusinessController::CATEGORYTYPE) {
            $sql .= "LEFT JOIN fund_category fc ON fl.CategoryId=fc.id AND fc.`Status`!=-1 WHERE fl.`Status`=0 AND fc.id={$this->id}";
        }elseif ($this->type == \backend\controllers\BusinessController::HOTTYPE) {
            $sql .= "WHERE fl.`Status`=0";
        }else{
            $sql .= "WHERE fl.`Status`=0";
        }

        // 计算满足查询条件的总记录数（得在分页sql前）
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY fl.IsTop DESC, fl.UpdateTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // 查询列表数据
        $sqlList = "SELECT *,fl.id,fl.UpdateTime,fl.InsertTime".$sql;
        $command = self::getDb()->createCommand($sqlList);
        $data = $command->queryAll();
        
        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['Recommend'] == '1') {
                    $value['RecommendName'] = '已推荐';
                }else{
                    $value['RecommendName'] = '未推荐';
                }
            }

            // 列表分页附加数据
            $data['totalRecords'] = $totalRecords; 
            $data['totalPages'] = ceil($data['totalRecords']/$this->pageSize); 
            $data['page'] = $this->page;
        }
        
        return $data;
    }

    /**
     * 添加自定义基金
     */
    public function addFund()
    {
        $instid = Yii::$app->admin->instid;

        $tableName = self::getTable($instid);

        // 基金代码是否在可购买基金列表中
        if (!$fundInfo = $this->getFundInfo($this->fundCode)) {
            return CommFun::renderFormat('202');
        }
        
        // 1-主题 2-类型 3-热销
        if ($this->type == \backend\controllers\BusinessController::THEMETYPE) {
            $ThemeId = $this->id;
            $condition = "FundCode='{$this->fundCode}' AND Status=0 AND ThemeId>0";
        }elseif ($this->type == \backend\controllers\BusinessController::CATEGORYTYPE) {
            $CategoryId = $this->id;
            $condition = "FundCode='{$this->fundCode}' AND Status=0 AND CategoryId>0";
        }else{
            $ThemeId = 0;
            $CategoryId = 0;
            $condition = ['FundCode'=>$this->fundCode,'Status'=>0];
        }

        // 基金代码是否已添加
        if ((new Query)->from($tableName)->where($condition)->one()) {
            return CommFun::renderFormat('201');
            // UPDATE
            /*$rs = self::getDb()->createCommand()->update($tableName, [
                'ThemeId' => isset($ThemeId)?$ThemeId:0,
                'CategoryId' => isset($CategoryId)?$CategoryId:0,
            ],['FundCode' => $this->fundCode])->execute();*/
        }else{
            $SecuAbbr = (new Query)->select(['SecuAbbr'])->from('SecuMain')->where(['SecuCode'=>$this->fundCode,'SecuCategory'=>8])->scalar(self::getDb('db_juyuan'));

            // INSERT
            $rs = self::getDb()->createCommand()->insert($tableName, [
                'ThemeId' => isset($ThemeId)?$ThemeId:0,
                'CategoryId' => isset($CategoryId)?$CategoryId:0,
                'FundCode' => $this->fundCode,
                'FundName' => $this->fundName?:$fundInfo['fundname'],
                'FundAbbrName' => $SecuAbbr?:'',
                'FundType' => $fundInfo['fundtype'],
                'Tags' => '',
            ])->execute();
        }
        
        return $rs;
    }

    /**
     * 更新基金
     */
    public function update($id, $tags)
    {
        $instid = Yii::$app->admin->instid;
        $tableName = self::getTable($instid);

        // 判断数据是否存在
        if (!(new Query)->from($tableName)->where(['id'=>$id])->one()) {
            return CommFun::renderFormat('202');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Tags' => $tags,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $id])->execute();

        
        return $rs;
    }

    /**
     * 删除基金
     */
    public function delete($id)
    {
        $instid = Yii::$app->admin->instid;
        $tableName = self::getTable($instid);

        // 判断数据是否存在
        if (!(new Query)->from($tableName)->where(['id'=>$id])->one()) {
            return CommFun::renderFormat('202');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Status' => -1
        ],['id' => $id])->execute();

        return $rs;
    }

    /**
     * 置顶基金
     */
    public function istop($id, $istop)
    {
        $instid = Yii::$app->admin->instid;
        $tableName = self::getTable($instid);
        
        // 判断数据是否存在
        if (!(new Query)->from($tableName)->where(['id'=>$id])->one()) {
            return CommFun::renderFormat('202');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'IsTop' => $istop,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $id])->execute();
        
        return $rs;
    }

    /**
     * 推荐基金
     */
    public function recommend($id,$recommend)
    {
        $instid = Yii::$app->admin->instid;
        $tableName = self::getTable($instid);
        
        // 判断数据是否存在
        if (!(new Query)->from($tableName)->where(['id'=>$id])->one()) {
            return CommFun::renderFormat('202');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update($tableName, [
            'Recommend' => $recommend,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['id' => $id])->execute();
        
        return $rs;
    }
}
