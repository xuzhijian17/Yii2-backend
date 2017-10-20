<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Portfolio model.
*/
class Portfolio extends BaseModel
{
    public $instid;
    public $PortfolioName;
    public $OtherInfo;
    public $low;
    public $mid;
    public $high;

    public $PortfolioId;
    public $fundname;
    public $fundcode;
    public $ratio;
    public $minpurchaseamount;
    public $reason;

    public $Status;

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
        return 'portfolio_config'.$instid;
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
            ['instid','default','value'=>Yii::$app->admin->instid],
            // [['PortfolioId','PortfolioName'],'validatePortfolio','on'=>['addPortfolio','editPortfolio','delPortfolio']],
            // default scenario validate rule.
            ['page','default','value'=>1],
            ['pageSize','default','value'=>Yii::$app->params['pageSize']],
            // addPortfolio scenario validate rule.
            ['PortfolioName','required','on'=>'addPortfolio'],
            // editPortfolio scenario validate rule.
            ['PortfolioId','required','on'=>'editPortfolio'],
            // delPortfolioFund scenario validate rule.
            ['PortfolioId','required','on'=>'delPortfolio'],
            // onlinePortfolio scenario validate rule.
            [['PortfolioId','Status'],'required','on'=>'onlinePortfolio'],
            ['Status','in','range'=>[0,1],'on'=>'onlinePortfolio'],
            // portfolioFundList scenario validate rule.
            ['PortfolioId','required','on'=>'portfolioFundList'],
            // addPortfolioFund scenario validate rule.
            ['PortfolioId','required','on'=>'addPortfolioFund']
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
            'default' => ['instid','page', 'pageSize'],
            'addPortfolio' => ['instid','PortfolioName', 'OtherInfo','low','mid','high'],
            'editPortfolio' => ['instid','PortfolioId','PortfolioName', 'OtherInfo','low','mid','high'],
            'delPortfolio' => ['instid','PortfolioId'],
            'onlinePortfolio' => ['instid','PortfolioId','Status'],
            'portfolioFundList' => ['instid','PortfolioId']
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'PortfolioId' || $attribute == 'PortfolioName') {
            $this->_errors = CommFun::renderFormat('206',[],$error);
        }elseif($attribute == 'Status'){
            $this->_errors = CommFun::renderFormat('101',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }

    /**
     * Verify portfolio whether there exist
     * @return [boolean]
     */
    public function isPortfolio($condition=[])
    {
        $isPortfolio = (new Query)
            ->from(self::getTable())
            ->where($condition)
            ->one()
        ;

        return $isPortfolio ? true : false;
    }

    /**
     * Portfolio list
     * @return [array]
     */
    public function portfolioList()
    {
        $tableName = self::getTable();
        
        // Init sql
        $sql = " FROM {$tableName} pf WHERE pf.Instid={$this->instid} AND pf.Status IN (0,1)";

        // Get paging total records(before paging limit)
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();
        
        // Order and Paging
        $sql .= " ORDER BY pf.UpdateTime DESC LIMIT ".(($this->page - 1) * $this->pageSize).",".$this->pageSize;

        // Select condition
        $sqlList = "SELECT *".$sql;
        $command = self::getDb()->createCommand($sqlList);
        $data = $command->queryAll();
        
        if ($data) {
            foreach ($data as $key => &$value) {
                if ($value['Status'] == '1') {
                    $value['StatusName'] = '已上线';
                }elseif($value['Status'] == '-1'){
                    $value['StatusName'] = '已删除';
                }else{
                    $value['StatusName'] = '未上线';
                }

                $value['ExpectInfo'] = json_decode($value['ExpectInfo'],true);
                $value['FundList'] = json_decode($value['FundList'],true);

                if (!empty($value['FundList'])) {
                    foreach ($value['FundList'] as $k => &$v) {
                        $limitInfo = \frontend\modules\api\services\QueryServiceApi::GetLimitInfo($v['fundcode']);
                        $v['lowestsumll'] = $limitInfo['minpurchaseamount'];    // Portfolio fund purchase amount
                    }

                    $value['MinSum'] = round(\frontend\modules\api\models\PortfolioConfig::getPortfolioMinSum($value['FundList']),2);   // Portfolio purchase amount
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
     * Portfolio fund list
     */
    public function portfolioFundList()
    {
        $rs = [];
        $FundList = (new Query)->select('FundList')->from(self::getTable())->where(['PortfolioId'=>$this->PortfolioId,'Instid'=>$this->instid])->scalar();
        if ($FundList) {
            $rs = json_decode($FundList,true);
            foreach ($rs as $k => &$v) {
                $limitInfo = \frontend\modules\api\services\QueryServiceApi::GetLimitInfo($v['fundcode']);
                $v['minpurchaseamount'] = $limitInfo['minpurchaseamount'];  // Portfolio fund purchase amount
            }
        }
        
        return $rs;
    }

    /**
     * Add portfolio
     */
    public function addPortfolio($value='')
    {
        // Verify portfolio whether there exist
        if ($this->isPortfolio(['PortfolioName'=>$this->PortfolioName,'Instid'=>$this->instid,'Status'=>[0,1]])) {
            return CommFun::renderFormat('205');
        }

        // INSERT
        $rs = self::getDb()->createCommand()->insert(self::getTable(), [
            'PortfolioName' => $this->PortfolioName?:'',
            'OtherInfo' => $this->OtherInfo?:'',
            'ExpectInfo' => json_encode(['low'=>$this->low,'mid'=>$this->mid,'high'=>$this->high]), // transfer expect info.
            'Instid' => $this->instid,
            'Ctime' => date("Y-m-d H:i:s"),
            'UpdateTime' => date("Y-m-d H:i:s"),
        ])->execute();
        

        return $rs;
    }

    /**
     * Edit portfolio
     */
    public function editPortfolio($value='')
    {
        // Verify portfolio whether there exist
        if (!$this->isPortfolio(['PortfolioId'=>$this->PortfolioId,'Instid'=>$this->instid,'Status'=>[0,1]])) {
            return CommFun::renderFormat('206');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update(self::getTable(), [
            'PortfolioName' => $this->PortfolioName?:'',
            'OtherInfo' => $this->OtherInfo?:'',
            'ExpectInfo' => json_encode(['low'=>$this->low,'mid'=>$this->mid,'high'=>$this->high]), // transfer expect info.
            'UpdateTime' => date("Y-m-d H:i:s"),
        ],['PortfolioId' => $this->PortfolioId])->execute();
        

        return $rs;
    }

    /**
     * Delete portfolio
     */
    public function delPortfolio()
    {
        // Verify portfolio whether there exist
        if (!$this->isPortfolio(['PortfolioId'=>$this->PortfolioId,'Instid'=>$this->instid,'Status'=>[0,1]])) {
            return CommFun::renderFormat('206');
        }

        // UPDATE
        $rs = self::getDb()->createCommand()->update(self::getTable(), [
            'Status' => -1,
            'UpdateTime' => date("Y-m-d H:i:s")
        ],['PortfolioId' => $this->PortfolioId])->execute();

        return $rs;
    }

    /**
     * Online portfolio
     */
    public function onlinePortfolio()
    {
        // Verify portfolio whether there exist
        if (!$this->isPortfolio(['PortfolioId'=>$this->PortfolioId,'Instid'=>$this->instid,'Status'=>[0,1]])) {
            return CommFun::renderFormat('206');
        }
        
        // UPDATE
        $rs = self::getDb()->createCommand()->update(self::getTable(), [
            'Status' => $this->Status,
            'Instid' => $this->instid,
            'UpdateTime' => date("Y-m-d H:i:s"),
        ],['PortfolioId' => $this->PortfolioId])->execute();

        return $rs;
    }

    /**
     * Add portfolio fund
     */
    public function addPortfolioFund($data='')
    {
        $instid = Yii::$app->admin->instid;

        if (isset($data['PortfolioId']) && !empty($data['PortfolioId'])) {
            $PortfolioId = $data['PortfolioId'];
            unset($data['PortfolioId']);
        }else{
            return false;
        }

        if (empty($data)) {
            return false;
        }
        
        // Portfolio purchase amount    
        $MinSum = \frontend\modules\api\models\PortfolioConfig::getPortfolioMinSum($data);
        
        // UPDATE
        $rs = self::getDb()->createCommand()->update(self::getTable(), [
            'FundList' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'MinSum' => $MinSum?:0,
            'Instid' => $instid,
            'UpdateTime' => date("Y-m-d H:i:s"),
        ],['PortfolioId' => $PortfolioId])->execute();

        return $rs;
    }
}
