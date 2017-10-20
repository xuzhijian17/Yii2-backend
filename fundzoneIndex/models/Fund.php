<?php
namespace fundzone\models;

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
     * 获取基金详情
     */
    public function getFundDetail($fundCode)
    {
        $data = CommFun::GetFundInfo($fundCode);

        if ($data && !empty($data)) {
            $data['TradingDay'] = date("Y-m-d",strtotime($data['TradingDay']));
            // 格式化风险等级
            if ($data['FundRiskLevel'] == '0') {
                $data['FundRiskLevelClass'] = 'risk05';
                $data['FundRiskLevelName'] = '低风险';
            }elseif ($data['FundRiskLevel'] == '1') {
                $data['FundRiskLevelClass'] = 'risk04';
                $data['FundRiskLevelName'] = '中低风险';
            }elseif ($data['FundRiskLevel'] == '2') {
                $data['FundRiskLevelClass'] = 'risk03';
                $data['FundRiskLevelName'] = '中风险';
            }elseif ($data['FundRiskLevel'] == '3') {
                $data['FundRiskLevelClass'] = 'risk02';
                $data['FundRiskLevelName'] = '中高风险';
            }elseif ($data['FundRiskLevel'] == '4') {
                $data['FundRiskLevelClass'] = 'risk01';
                $data['FundRiskLevelName'] = '高风险';
            }else{
                $data['FundRiskLevelClass'] = '';
                $data['FundRiskLevelName'] = '';
            }

            // 格式化基金类型样式
            if ($data['FundTypeCode'] == '1101') {
                $data['FundTypeClass'] = 'tagGp';
            }elseif ($data['FundTypeCode'] == '1103') {
                $data['FundTypeClass'] = 'tagHh';
            }elseif ($data['FundTypeCode'] == '1105') {
                $data['FundTypeClass'] = 'tagZq';
            }elseif ($data['FundTypeCode'] == '1106') {
                $data['FundTypeClass'] = 'tagLc';
            }elseif ($data['FundTypeCode'] == '1109') {
                $data['FundTypeClass'] = 'tagHb';
            }elseif ($data['FundTypeCode'] == '1110') {
                $data['FundTypeClass'] = 'tagQdii';
            }else{
                $data['FundTypeClass'] = '';
            }
            
            // 基金星级
            $sql = "SELECT * FROM MF_FundRating WHERE FundInnerCode=:InnerCode AND EndDate=(SELECT MAX(EndDate) AS MAX_EndDate FROM MF_FundRating WHERE FundInnerCode=:InnerCode)";
            $command = self::getDb('db_juyuan')->createCommand($sql);
            $command->bindParam(":InnerCode", $data['InnerCode']);
            $starRankData = $command->queryOne();
            
            if ($starRankData && !empty($starRankData)) {
                if ($starRankData['StarRank'] == '1') {
                    $data['StarRankClass'] = 'stars01';
                }elseif ($starRankData['StarRank'] == '2') {
                    $data['StarRankClass'] = 'stars02';
                }elseif ($starRankData['StarRank'] == '3') {
                    $data['StarRankClass'] = 'stars03';
                }elseif ($starRankData['StarRank'] == '4') {
                    $data['StarRankClass'] = 'stars04';
                }elseif ($starRankData['StarRank'] == '5') {
                    $data['StarRankClass'] = 'stars05';
                }elseif ($starRankData['StarRank'] == '6') {
                    $data['StarRankClass'] = 'stars05';
                }else{
                    $data['StarRankClass'] = '';
                }
            }
        }

        return $data;
    }


    /**
    * 收益走势
    * @return array|boolean
    */
    public function getNetValue($fundCode, $startDay='')
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        $startDay = $startDay ?: '1970-01-01'; 

        $sql = "SELECT * FROM MF_NetValue WHERE InnerCode=:InnerCode AND EndDate>=DATE_FORMAT(:startDay,'%Y-%m-%d') ORDER BY EndDate ASC";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $command->bindParam(":startDay", $startDay);
        $data = $command->queryAll();

        $kLineChart = [];
        if ($data && !empty($data)) {
            foreach ($data as $key => &$value) {
                $kLineChart['EndDate'][] = date("Y-m-d",strtotime($value['EndDate']));
                if ($fundInfo['FundTypeCode']=='1109' || $fundInfo['FundTypeCode']=='1106') {
                    $kLineChart['data'][] = round($value['DailyProfit'],4);
                }else{
                    $kLineChart['data'][] = round($value['NVDailyGrowthRate'],4);
                }
            }
        }
        
        return $kLineChart;
    }

    /**
    * 历史净值
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public function getHistoryNetValue($fundCode, $startDay=null, $endDay=null)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        $startDay = $startDay ?: date("Y-m-d",time()-3600*24*365);
        $endDay = $endDay ?:  date("Y-m-d");
        
        $sql = "SELECT * FROM MF_NetValuePerformanceHis WHERE InnerCode=:InnerCode AND TradingDay>=DATE_FORMAT(:startDay,'%Y-%m-%d') AND TradingDay<=DATE_FORMAT(:endDay,'%Y-%m-%d') ORDER BY TradingDay DESC";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $command->bindParam(":startDay", $startDay);
        $command->bindParam(":endDay", $endDay);
        $data = $command->queryAll();
        
        return $data;
    }

    /**
    * 基金经理
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    public function fundManager($fundCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 基金经理
        $data = (new Query())
            ->from('MF_FundManager')
            ->where(['InnerCode' => $fundInfo['InnerCode']])
            ->all(self::getDb('db_juyuan'))
        ;

        if ($data && !empty($data)) {
            foreach ($data as $key => &$value) {
                $value['AccessionDate'] = date("Y-m-d",strtotime($value['AccessionDate']));
                $value['DimissionDate'] = $value['DimissionDate'] ? date("Y-m-d",strtotime($value['DimissionDate'])) : '至今';
            }
        }

        return $data;
    }

    /**
     * 交易须知
     */
    public function profitGuide($fundCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        $data = [];
        if ($fundInfo && !empty($fundInfo)) {
            // 收费模式状态
            if ($fundInfo['ShareType'] == 'A') {
                $data['ShareTypeName'] = '前端收费';
            }elseif ($data['ShareType'] == 'B') {
                $data['ShareTypeName'] = '后端收费';
            }else{
                $data['ShareTypeName'] = '其它';
            }

            // 购买状态
            $data['buyStatus'] = in_array($fundInfo['FundState'], [0,1,2,6,7,8]) ?: false;  //购买状态：true-可买，false-不可买
            $data['sellStatus'] = in_array($fundInfo['FundState'], [0,1,2,5,7,8]) ?: false; //赎回状态：true-可赎，false-不可赎
            $data['buyStatusName'] = $data['buyStatus'] ? '开放购买' : '暂不开放'; 
            $data['sellStatusName'] = $data['sellStatus'] ? '开放购买' : '暂不开放';

            // 认购/申购状态判断
            if (in_array($fundInfo['FundState'], [1,2])) {  //认购
                $data['fundState'] = 1;
                $data['fundStateName'] = '认购';
                $data['minAmount'] = $fundInfo['MinSubscribAmount'];
                $chargeRateType = '10';
            }elseif (in_array($fundInfo['FundState'], [0,6,7,8])) { //申购
                $data['fundState'] = 2;
                $data['fundStateName'] = '申购';
                $data['minAmount'] = $fundInfo['MinPurchaseAmount'];
                $chargeRateType = '11';
            }else{
                $data['fundState'] = 0;
                $data['fundStateName'] = '申购';
                $data['minAmount'] = $fundInfo['MinPurchaseAmount'];
                $chargeRateType = '11';
            }

            // 认/申购费率
            $data['buyChargeRate'] = $this->chargeRate($fundCode,$chargeRateType);
            // 赎回费率
            $data['sellChargeRate'] = $this->chargeRate($fundCode,'12');
            // 管理费
            $data['manageChargeRate'] = $this->chargeRate($fundCode,'15');
            // 托管费
            $data['trusteeshipChargeRate'] = $this->chargeRate($fundCode,'16');
        }
        
        return $data;
    }

    /**
     * 基金费率
     */
    public function chargeRate($fundCode,$chargeRateType)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);
        
        $sql = "SELECT * FROM MF_ChargeRateNew WHERE InnerCode=:InnerCode AND IfExecuted=1 AND ChargeRateCur=1420 AND ClientType=10 AND LEFT(ChargeRateType,2)=:ChargeRateType AND RIGHT(ChargeRateType,1)=0 ORDER BY StDivStand1 ASC";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $command->bindParam(":ChargeRateType", $chargeRateType);
        $data = $command->queryAll();
        
        return $data;
    }
	
	/**
    * 基金概况
    * @param [string] 基金内部编码
    * @return array
    */
    public function fundArchives($fundCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 基金概况
        $data = (new Query())
            ->from('MF_FundArchives')
            ->where(['InnerCode' => $fundInfo['InnerCode']])
            ->one(self::getDb('db_juyuan'))
        ;

        if ($data && !empty($data)) {
            $data['EstablishmentDate'] = date("Y-m-d",strtotime($data['EstablishmentDate']));
            $data['FundType'] = $fundInfo['FundType'];
            // $data['InvestAdvisorName'] = $fundInfo['InvestAdvisorName'];

            // 基金管理人
            $sql = "SELECT InvestAdvisorName FROM MF_InvestAdvisorOutline WHERE InvestAdvisorCode=:InvestAdvisorCode";
            $command = self::getDb('db_juyuan')->createCommand($sql);
            $command->bindParam(":InvestAdvisorCode", $data['InvestAdvisorCode']);
            $data['InvestAdvisorName'] = $command->queryScalar()?:'';

            // 基金托管人
            $sql = "SELECT TrusteeName FROM MF_TrusteeOutline WHERE TrusteeCode=:TrusteeCode";
            $command = self::getDb('db_juyuan')->createCommand($sql);
            $command->bindParam(":TrusteeCode", $data['TrusteeCode']);
            $data['TrusteeName'] = $command->queryScalar()?:'';
        }

        return $data;
    }

    /**
    * 持仓信息
    * @return array|boolean
    */
    public function positionInfo($fundCode)
    {
        $data = [];

        // 资产配置
        $data['assetAllocation'] = $this->assetAllocation($fundCode,[10020, 10090, 1000202]);
        // 行业配置
        $data['investIndustry'] = $this->investIndustry($fundCode);
        // 重仓股
        $data['keyStockPortfolio'] = $this->keyStockPortfolio($fundCode);

        return $data;
    }

    /**
    * 资产配置
    * @return array|boolean
    */
    public function assetAllocation($fundCode,$assetTypeCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 最新报告日期
        $sql = "SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_AssetAllocation WHERE InnerCode=:InnerCode";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $ReportDate = $command->queryScalar();

        // 资产配置
        $data = (new Query())
            ->from('MF_AssetAllocation')
            ->where(['InnerCode'=>$fundInfo['InnerCode'],'ReportDate'=>$ReportDate,'AssetTypeCode'=>$assetTypeCode])
            ->all(self::getDb('db_juyuan'))
        ;

        return $data;
    }

    /**
    * 行业配置
    * @return array|boolean
    */
    public function investIndustry($fundCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 最新报告日期
        $sql = "SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_InvestIndustry WHERE InnerCode=:InnerCode";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $ReportDate = $command->queryScalar();

        // 行业配置
        $data = (new Query())
            ->from('MF_InvestIndustry')
            ->where(['InnerCode'=>$fundInfo['InnerCode'],'ReportDate'=>$ReportDate])
            ->all(self::getDb('db_juyuan'))
        ;

        return $data;
    }

    /**
    * 重仓股
    * @return array|boolean
    */
    public function keyStockPortfolio($fundCode)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 最新报告日期
        $sql = "SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_KeyStockPortfolio WHERE InnerCode=:InnerCode";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $ReportDate = $command->queryScalar();
        
        // 重仓股
        /*if ($fundInfo['InnerCode']<1000000) {
            $sql = "SELECT * FROM MF_KeyStockPortfolio ksp INNER JOIN SecuMain s ON ksp.StockInnerCode=s.InnerCode WHERE ksp.InnerCode=:InnerCode AND ksp.ReportDate=:ReportDate";
        }elseif(1000000<$fundInfo['InnerCode']<2000000){
            $sql = "SELECT * FROM MF_KeyStockPortfolio ksp LEFT JOIN HK_SecuMain s ON ksp.StockInnerCode=s.InnerCode WHERE ksp.InnerCode=:InnerCode AND ksp.ReportDate=:ReportDate";
        }else{
            $sql = "SELECT * FROM MF_KeyStockPortfolio ksp LEFT JOIN SecuMain s ON ksp.StockInnerCode=s.InnerCode WHERE ksp.InnerCode=:InnerCode AND ksp.ReportDate=:ReportDate";
        }*/
        $sql = "SELECT * FROM MF_KeyStockPortfolio ksp INNER JOIN SecuMain s ON ksp.StockInnerCode=s.InnerCode WHERE ksp.InnerCode=:InnerCode AND ksp.ReportDate=:ReportDate";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $command->bindParam(":ReportDate", $ReportDate);
        $data = $command->queryAll();

        return $data;
    }

    /**
    * 基金公告
    * @param [string] 基金内部编码
    * @return array
    */
    public function fundBulletin($fundCode='')
    {
        $data = [];

        // 基金公告（临时）
        $interimBulletin = $this->interimBulletin($fundCode);
        // 基金公告（原文）
        $interimAnnouncement = $this->interimAnnouncement($fundCode);

        $data = $interimBulletin;   //array_merge($interimBulletin,$interimAnnouncement);
        
        if ($data && !empty($data)) {
            foreach ($data as $key => &$value) {
                $value['BulletinDate'] = date("Y-m-d",strtotime($value['BulletinDate']));
            }
        }

        return $data;
    }

    /**
    * 基金公告详情
    * @param [string] $type 0-原文公告，1-临时公告
    * @return array
    */
    public function bulletinDetail($id='',$type=0)
    {
        if (!$id) {
            return [];
        }

        if ($type) {
            $tableName = 'MF_InterimBulletin';  // 基金公告（临时）
            $sql = "SELECT *,Detail AS Content FROM MF_InterimBulletin WHERE ID=:id";
        }else{
            $tableName = 'MF_Announcement'; // 基金公告（原文）
            $sql = "SELECT *,EndDate AS BulletinDate FROM MF_Announcement WHERE ID=:id";
        }

        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":id", $id);
        $data = $command->queryOne();

        return $data;
    }
	
	/**
    * 基金公告（临时）
    * @param [string] 基金内部编码
    * @return array
    */
    public function interimBulletin($fundCode, $page=1, $pageSize=15)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        // 获取总记录数
        /*$sql = "SELECT COUNT(*) FROM MF_InterimBulletin ib LEFT JOIN MF_InterimBulletin_SE ibse ON ib.ID=ibse.ID WHERE ibse.`CODE`=:InnerCode AND ibse.TypeCode=1";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $totalRecords = $command->queryScalar();

        // 分页参数判断
        $pageSize = $pageSize < 1 || $pageSize > $totalRecords ? 15 : $pageSize;
        $page = $page < 1 ? 1 : $page;
        if ($page > ceil($totalRecords / $pageSize)) {
            return [];
        }*/

        // 临时公告
        $sql = "SELECT *,Detail AS Content FROM MF_InterimBulletin ib LEFT JOIN MF_InterimBulletin_SE ibse ON ib.ID=ibse.ID WHERE ibse.`CODE`=:InnerCode AND ibse.TypeCode=1 ORDER BY ib.BulletinDate DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $data = $command->queryAll();

        return $data;
    }

    /**
    * 基金公告（原文）
    * @param [string] 基金内部编码
    * @return array
    */
    public function interimAnnouncement($fundCode, $page=1, $pageSize=15)
    {
        $fundInfo = CommFun::GetFundInfo($fundCode);

        if (!$fundInfo || empty($fundInfo)) {
            return false;
        }

        /*// 获取总记录数
        $sql = "SELECT COUNT(*) FROM MF_Announcement WHERE InnerCode=:InnerCode";
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $totalRecords = $command->queryScalar();

        // 分页参数判断
        $pageSize = $pageSize < 1 || $pageSize > $totalRecords ? 15 : $pageSize;
        $page = $page < 1 ? 1 : $page;
        if ($page > ceil($totalRecords / $pageSize)) {
            return [];
        }*/

        // 原文公告
        $sql = "SELECT *,EndDate AS BulletinDate FROM MF_Announcement WHERE InnerCode=:InnerCode ORDER BY InfoPublDate DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $command = self::getDb('db_juyuan')->createCommand($sql);
        $command->bindParam(":InnerCode", $fundInfo['InnerCode']);
        $data = $command->queryAll();
        
        return $data;
    }

    /**
    * 基金分红
    * @param [string] 基金内部编码
    * @return array
    */
    public function participationProfit($fundCode,$page=1, $pageSize=15)
    {
        // 获取总记录数
        $sql = "SELECT COUNT(*) FROM dividend WHERE FundCode=:fundCode";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":fundCode", $fundCode);
        $totalRecords = $command->queryScalar();

        // 分页参数判断
        $pageSize = $pageSize < 1 || $pageSize > $totalRecords ? 15 : $pageSize;
        $page = $page < 1 ? 1 : $page;
        if ($page > ceil($totalRecords / $pageSize)) {
            return [];
        }

        $sql = "SELECT * FROM dividend WHERE FundCode=:fundCode ORDER BY InfoPublDate DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":fundCode", $fundCode);
        $data = $command->queryAll();

        if ($data && !empty($data)) {
            foreach ($data as $key => &$value) {
                $value['ExRightDate'] = date("Y-m-d",strtotime($value['ExRightDate']));
                $value['ExecuteDate'] = date("Y-m-d",strtotime($value['ExecuteDate']));
                $value['InfoPublDate'] = date("Y-m-d",strtotime($value['InfoPublDate']));
            }
        }
        
        return $data;
    }
}
