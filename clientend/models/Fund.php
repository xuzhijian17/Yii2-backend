<?php
namespace clientend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\CommFun;

/**
* Fund model.
*/
class Fund extends Model
{
    public $id;
	public $tid;
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
     * 获取推荐基金
     */
    public static function getRecommend()
    {
        $sql = "SELECT * FROM fund_list_0 fl LEFT JOIN fund_info fi ON fl.FundCode=fi.FundCode WHERE fl.Status=0 AND fl.Recommend=1 ORDER BY fl.UpdateTime DESC";
        $command = Yii::$app->db->createCommand($sql);
        return $command->queryAll();
    }

    /**
     * 获取推荐主题
     */
    public static function getRecommendTheme()
    {
		$sql = "SELECT * FROM fund_theme WHERE Recommend=1 AND Status=1 ORDER BY UpdateTime DESC";
		$command = Yii::$app->db->createCommand($sql);
        return $command->queryAll();
    }
	
    /**
     * 获取主题详情（主题基金列表）
     */
    public static function getThemeDetail($tid, $page=1, $pageSize=15)
    {
        


        // 主题基金sql
        $sql = " FROM fund_list_0 fl LEFT JOIN fund_theme ft ON fl.ThemeId=ft.id AND fl.`Status`!=-1 WHERE fl.`Status`=0 AND ft.id={$tid}";

        // 计算满足查询条件的总记录数（得在分页sql前）
        $command = self::getDb()->createCommand("SELECT COUNT(*)".$sql);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        $sql .= " ORDER BY fl.IsTop DESC, fl.UpdateTime DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;

        // 查询列表数据
        $sqlList = "SELECT *,fl.id".$sql;
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
            $data['totalPages'] = ceil($totalRecords/$pageSize); 
            $data['page'] = $page;
        }

        // 主题介绍
        $sql = "SELECT * FROM fund_theme WHERE Status=1 AND id=:tid";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":tid",$tid);
        $themeData = $command->queryOne();
        if ($themeData) {
            $data['title'] = $themeData['Theme'];
            $data['descript'] = $themeData['Describe'];
        }else{
            $data['title'] = '';
            $data['descript'] = '';
        }
        
        
        return $data;
    }

    /**
     * 获取基金详情
     */
    public static function getFundDetail($fundCode)
    {
        $data = CommFun::GetFundInfo($fundCode);
        // var_dump($data);
        /*$sql = "SELECT * FROM fund_info WHERE id=:id";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$this->id);
        return $command->queryOne();*/
    }

    /**
     * 获取基金超市
     */
    public static function fundList($FundTypeCode='', $page=1, $pageSize=15)
    {
        $fundList = [];

        if ($FundTypeCode) {
            $fundData = (new Query()) 
                ->from('fund_info')
                ->where(['FundTypeCode'=>$FundTypeCode])
                ->andWhere(['<','MinPurchaseAmount','1000000'])
                ->andWhere(['<','MinSubscribAmount','1000000'])
                ->all(self::getDb('db_local'))
            ;
        }else{
            $fundData = (new Query())
                ->from('fund_info')
                ->andWhere(['<','MinPurchaseAmount','1000000'])
                ->andWhere(['<','MinSubscribAmount','1000000'])
                ->all(self::getDb('db_local'))
            ;
        }
        
        
        /*if ($fundData) {
            foreach ($fundData as $key => $value) {
                if ($value['ShareType'] === 'B') {
                    continue;
                }
                // 返回数据字段及格式转换
                $tmpArr['fundcode'] = $value['FundCode'];
                $tmpArr['fundname'] = $value['FundName'];
                $tmpArr['fundtype'] = $value['FundType'];
                $tmpArr['fundstate'] = $value['FundState'];
                $tmpArr['sharetype'] = $value['ShareType'];
                $tmpArr['fundnature'] = isset($value['FundNature'])?$value['FundNature']:1;
                $tmpArr['tradingday'] = date("Y-m-d",strtotime($value['TradingDay']));
                $tmpArr['unitnv'] = isset($value['PernetValue']) ? round($value['PernetValue'],4) : round(0,2);
                $tmpArr['dailyprofit'] = isset($value['DailyProfit']) ? round($value['DailyProfit'],2) : round(0,2);  // 万份收益
                $tmpArr['latestweeklyyield'] = isset($value['LatestWeeklyYield']) ? round($value['LatestWeeklyYield'],4) : round(0,2);    // 七日年化收益
                $tmpArr['dailygrowth'] = isset($value['NVDailyGrowthRate']) ? round($value['NVDailyGrowthRate'],2) : round(0,2);
                $tmpArr['insingleweek'] = isset($value['RRInSingleWeek']) ? round($value['RRInSingleWeek'],2) : round(0,2);
                $tmpArr['insinglemonth'] = isset($value['RRInSingleMonth']) ? round($value['RRInSingleMonth'],2) : round(0,2);
                $tmpArr['inthreemonth'] = isset($value['RRInThreeMonth']) ? round($value['RRInThreeMonth'],2) : round(0,2);
                $tmpArr['insixmonth'] = isset($value['RRInSixMonth']) ? round($value['RRInSixMonth'],2) : round(0,2);
                $tmpArr['insingleyear'] = isset($value['RRInSingleYear']) ? round($value['RRInSingleYear'],2) : round(0,2);
                $fundList[] = $tmpArr;
            }
        }*/
        
        return $fundData;
    }

    /**
    * 净值走势（k线图）
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public static function getNetValue($fundCode, $startDay='')
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // $sql = "SELECT * FROM MF_NetValuePerformanceHis WHERE InnerCode=:InnerCode AND TradingDay>=DATE_FORMAT(:startDay,'%Y-%m-%d') ORDER BY TradingDay DESC";
        $sql = "SELECT * FROM MF_NetValue WHERE InnerCode=:InnerCode AND EndDate>=DATE_FORMAT(:startDay,'%Y-%m-%d') ORDER BY EndDate DESC";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $command->bindParam(":startDay", $startDay?:date('Y-m-d',time()-3600*24*30));
        $data = $command->queryAll();

        return $data;
    }

    /**
    * 历史净值
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public static function getHistoryNetValue($fundCode, $startDay, $endDay)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        $sql = "SELECT * FROM MF_NetValuePerformanceHis WHERE InnerCode=:InnerCode AND TradingDay>=DATE_FORMAT(:startDay,'%Y-%m-%d') AND TradingDay<=DATE_FORMAT(:endDay,'%Y-%m-%d') ORDER BY TradingDay DESC";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $command->bindParam(":startDay", $startDay);
        $command->bindParam(":endDay", $endDay);
        $historyNetValue = $command->queryAll();
        
        $newHistoryNetValue = [];
        if ($historyNetValue) {
            foreach ($historyNetValue as $key => $value) {
                $tmpHistoryNetValue['tradingday'] = $value['TradingDay'] ?: '';
                $tmpHistoryNetValue['unitnv'] = $value['UnitNV'] ?: '';
                $tmpHistoryNetValue['nvdailygrowthrate'] = $value['NVDailyGrowthRate'] ?: '';
                $tmpHistoryNetValue['rrinsingleweek'] = $value['RRInSingleWeek'] ?: '';
                $tmpHistoryNetValue['rrinsinglemonth'] = $value['RRInSingleMonth'] ?: '';
                $tmpHistoryNetValue['rrinthreemonth'] = $value['RRInThreeMonth'] ?: '';
                $tmpHistoryNetValue['rrinsixmonth'] = $value['RRInSixMonth'] ?: '';
                $tmpHistoryNetValue['rrinsingleyear'] = $value['RRInSingleYear'] ?: '';
                $tmpHistoryNetValue['rrsincestart'] = $value['RRSinceStart'] ?: '';
                $newHistoryNetValue[] = $tmpHistoryNetValue;
            }
        }
        
        return $newHistoryNetValue;
    }
}
