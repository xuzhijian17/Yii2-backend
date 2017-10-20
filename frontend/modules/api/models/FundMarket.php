<?php

namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\HundSun;
use common\lib\CommFun;
use frontend\services\TradeService;

class FundMarket extends Model
{
    /**
     * 定义对B端API基金类型的返回值
     */
    const STOCK_TYPE_CODE = 1;  // 股票型

    const MIX_TYPE_CODE = 2;    // 混合型
    
    const BOND_TYPE_CODE = 3;   // 债券型

    const SHORT_BOND_TYPE_CODE = 4;  //短期理财债券型

    const CURRENCY_TYPE_CODE = 5;   // 货币型

    const QDII_TYPE_CODE = 6;  // QDII型

    private $_secumain;

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
    public static function getDb($db_name='db_juyuan')
    {
        return $db_name ? Yii::$app->$db_name : Yii::$app->db;
    }

    /**
    * 基金主表数据
    * @param [string] fund code
    * @return fund detail of array data
    */
    public function getSecuMain($fundCode)
    {
        if ($this->_secumain === null) {
            // 基金代码
            $this->_secumain = (new Query())
                ->select(['*'])
                ->from('SecuMain')
                ->where(['SecuCategory' => 8, 'SecuCode'=>$fundCode])
                ->one(self::getDb())
            ;
            // $this->_secumain = CommFun::GetFundInfo($fundCode);
        }
        
        return $this->_secumain;
    }


    /**
    * 基金超市列表API接口的业务逻辑处理
    * @param [string] fund type
    * @param [int] page 
    * @param [int] page size
    * @return fund list of array data
    */
    public function fundList($fundType='', $page=1, $pageSize=15)
    {
        $fundList = [];

        // 判断不同基金类型，基金类型的转换
        switch ($fundType) {
            case self::STOCK_TYPE_CODE:
                $FundTypeCode = "1101";
                break;
            case self::MIX_TYPE_CODE:
                $FundTypeCode = "1103";
                break;
            case self::BOND_TYPE_CODE:
                $FundTypeCode = "1105";
                break;
            case self::SHORT_BOND_TYPE_CODE:
                $FundTypeCode = "1106";
                break;
            case self::CURRENCY_TYPE_CODE:
                $FundTypeCode = "1109";
                break;
            case self::QDII_TYPE_CODE:
                $FundTypeCode = "1110";
                break;
            default:
                $FundTypeCode = '';
                break;
        }

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
        
        
        if ($fundData) {
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
        }
        
        return $fundList;
    }

    
    /**
    * 基金详情API业务逻辑处理
    * @param [string] fund code
    * @return fund detail of array data
    */
    public function fundDetail($fundCode)
    {
        $data = CommFun::GetFundInfo($fundCode);

        if (!$data || empty($data)) {
            CommFun::handleCode('-100');
        }

        $fundData = [];
        $fundData['fundcode'] = isset($data['FundCode']) ? $data['FundCode'] : '';
        $fundData['fundname'] = isset($data['FundName']) ? $data['FundName'] : '';
        $fundData['fundtype'] = isset($data['FundType']) ? $data['FundType'] : '';
        $fundData['lowestsumsubscribing'] = '';
        $fundData['lowestsumsubll'] = isset($data['MinSubscribAmount']) ? $data['MinSubscribAmount'] : '';
        $fundData['lowestsumpurll'] = isset($data['MinPurchaseAmount']) ? $data['MinPurchaseAmount'] : '';
        $fundData['unitnv'] = isset($data['PernetValue']) ? round($data['PernetValue'],4) : 0;
        $fundData['dailyprofit'] = isset($data['DailyProfit']) ? round($data['DailyProfit'],2) : 0;
        $fundData['latestweeklyyield'] = isset($data['LatestWeeklyYield']) ? round($data['LatestWeeklyYield'],2) : 0;
        $fundData['nvdailygrowthrate'] = isset($data['NVDailyGrowthRate']) ? round($data['NVDailyGrowthRate'],4) : 0;
        $fundData['rrsincethisyear'] = isset($data['RRSinceThisYear']) ? round($data['RRSinceThisYear'],4) : 0;
        $fundData['rrsincestart'] = isset($data['RRSinceStart']) ? round($data['RRSinceStart'],4) : 0;
        $fundData['riskevaluation'] = CommFun::toRiskEvaluationHs(isset($data['FundRiskLevel']) ? $data['FundRiskLevel'] : 99);

        // 基金星级
        $FundRating = (new Query())
            ->select(['StarRank'])
            ->from('MF_FundRating')
            ->where(['FundInnerCode' => $data['InnerCode']])
            ->one(self::getDb())
        ;
        $fundData['starrank'] = isset($FundRating['StarRank']) ? $FundRating['StarRank'] : 99;
        
        return $fundData;
    }

    /**
    * 基金评级
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    public function fundRating($fundCode)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 基金评级、星级
        $FundRating = (new Query())
            ->select(['*'])
            ->from('MF_FundRating')
            ->where(['FundInnerCode' => $SecuMain['InnerCode']])
            ->one(self::getDb())
        ;

        $NewFundRating = [];
        $NewFundRating['riskevaluation'] = CommFun::toRiskEvaluation(isset($FundRating['RiskEvaluation']) ? $FundRating['RiskEvaluation'] : 99);
        $NewFundRating['starrank'] = isset($FundRating['StarRank']) ? $FundRating['StarRank'] : 99;

        return $NewFundRating;
    }

    /**
    * 基金费率
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    /*public function chargeRate($fundCode, $chargeRateType)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 基金费率
        $sql = "SELECT * FROM MF_ChargeRate cr LEFT JOIN MF_ChargeRate_SE crs ON cr.ID=crs.ID AND crs.TypeCode=3 AND crs.`Code`=10 WHERE cr.InnerCode=:InnerCode AND cr.IfExecuted=1 AND cr.ChargeRateType=:ChargeRateType AND (ExcuteDate=(SELECT MAX(ExcuteDate) AS MAX_ExcuteDate FROM MF_ChargeRate WHERE InnerCode=:InnerCode AND ChargeRateType=:ChargeRateType ORDER BY ExcuteDate DESC) OR ExcuteDate IS NULL) ORDER BY cr.ExcuteDate DESC";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $command->bindParam(":ChargeRateType", $chargeRateType);
        $ChargeRate = $command->queryAll();

        $NewChargeRate = [];
        if ($ChargeRate) {
            foreach ($ChargeRate as $key => $value) {
                $TmpChargeRate['beginofapplysuminterval'] = $value['BeginOfApplySumInterval'] ?: '';
                $TmpChargeRate['endofapplysuminterval'] = $value['EndOfApplySumInterval'] ?: '';
                $TmpChargeRate['chargeratetype'] = $value['ChargeRateType'] ?: '0';
                $TmpChargeRate['chargepattern'] = $value['ChargePattern'] ?: '0';
                $TmpChargeRate['chargeratedesciption'] = $value['ChargeRateDesciption'] ?: '';
                $TmpChargeRate['intervaldescription'] = $value['IntervalDescription'] ?: '';
                $TmpChargeRate['minimumchargerate'] = round($value['MinimumChargeRate'],4) ?: '';
                $TmpChargeRate['maximumchargerate'] = round($value['MaximumChargeRate'],4) ?: '';
                $TmpChargeRate['excutedate'] = $value['ExcuteDate'] ?: '';
                $TmpChargeRate['notes'] = $value['Notes'] ?: '';
                $NewChargeRate[] = $TmpChargeRate;
            }
        }
        
        return $NewChargeRate;
    }*/
    public function chargeRate($fundCode, $chargeRateType)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        if ($chargeRateType == '1') {
            $chargeRateType = '10';
        }elseif ($chargeRateType == '2') {
            $chargeRateType = '11';
        }elseif ($chargeRateType == '3') {
            $chargeRateType = '12';
        }else{
            CommFun::handleCode('-9');
        }

        $sql = "SELECT * FROM MF_ChargeRateNew WHERE InnerCode=:InnerCode AND IfExecuted=1 AND ChargeRateCur=1420 AND ClientType=10 AND ShiftInTarget=0 AND LEFT (ChargeRateType,2)=:ChargeRateType ORDER BY StDivStand1 ASC";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $command->bindParam(":ChargeRateType", $chargeRateType);
        $ChargeRate = $command->queryAll();

        $NewChargeRate = [];
        if ($ChargeRate) {
            foreach ($ChargeRate as $key => $value) {
                // 过滤场外后端费率
                if (substr($value['ChargeRateType'],2,1) == 1 || substr($value['ChargeRateType'],3,1) == 2) {
                    continue;
                }
                $DivStand1 = substr($value['ChargeRateDiv'],0,3);
                $DivStand2 = substr($value['ChargeRateDiv'],3,3);
                $DivStand3 = substr($value['ChargeRateDiv'],6,3);
                if (substr($DivStand1,0,2) == '11') {
                    $TmpChargeRate['beginofapplysuminterval'] = $value['StDivStand1'] ?: '';
                    $TmpChargeRate['endofapplysuminterval'] = $value['EnDivStand1'] ?: '';
                    if ($value['DivStandUnit1'] == '4') {
                        $TmpChargeRate['beginofapplysuminterval'] = !empty($value['StDivStand1']) ? (int) $value['StDivStand1']*10000 : '';
                        $TmpChargeRate['endofapplysuminterval'] = !empty($value['EnDivStand1']) ? (int) $value['EnDivStand1']*10000 : '';
                    }
                }elseif (substr($DivStand2,0,2) == '11') {
                    $TmpChargeRate['beginofapplysuminterval'] = $value['StDivStand2'] ?: '';
                    $TmpChargeRate['endofapplysuminterval'] = $value['EnDivStand2'] ?: '';
                    if ($value['DivStandUnit1'] == '4') {
                        $TmpChargeRate['beginofapplysuminterval'] = !empty($value['StDivStand1']) ? (int) $value['StDivStand1']*10000 : '';
                        $TmpChargeRate['endofapplysuminterval'] = !empty($value['EnDivStand1']) ? (int) $value['EnDivStand1']*10000 : '';
                    }
                }elseif (substr($DivStand3,0,2) == '11') {
                    $TmpChargeRate['beginofapplysuminterval'] = $value['StDivStand3'] ?: '';
                    $TmpChargeRate['endofapplysuminterval'] = $value['EnDivStand3'] ?: '';
                    if ($value['DivStandUnit1'] == '4') {
                        $TmpChargeRate['beginofapplysuminterval'] = !empty($value['StDivStand1']) ? (int) $value['StDivStand1']*10000 : '';
                        $TmpChargeRate['endofapplysuminterval'] = !empty($value['EnDivStand1']) ? (int) $value['EnDivStand1']*10000 : '';
                    }
                }else{
                    $TmpChargeRate['beginofapplysuminterval'] = '';
                    $TmpChargeRate['endofapplysuminterval'] = '';
                }
                $TmpChargeRate['chargeratetype'] = $chargeRateType;
                $TmpChargeRate['chargepattern'] = '102';    // 前端收费模式(场外)
                $TmpChargeRate['chargeratedesciption'] = $value['ChargeRateDes'] ?: ''; //preg_replace('/(.*?)(\d+(\.\d+)?(\%|[\x{4e00}-\x{9fa5}])$)/iu', '$2', $value['ChargeRateDes']);
                $TmpChargeRate['intervaldescription'] = $value['DivIntervalDes'] ?: ''; //preg_replace('/(.*?)(\d+(\.\d+)?(\%|[\x{4e00}-\x{9fa5}])$)/iu', '$1', $value['ChargeRateDes']);
                $TmpChargeRate['minimumchargerate'] = round($value['MinChargeRate'],4);
                $TmpChargeRate['maximumchargerate'] = round($value['MaxChargeRate'],4);
                $TmpChargeRate['excutedate'] = $value['ExcuteDate'] ?: '';
                $TmpChargeRate['notes'] = $value['Notes'] ?: '';
                $NewChargeRate[] = $TmpChargeRate;
            }
        }

        if ($chargeRateType == '11') {
            return CommFun::multi_array_sort($NewChargeRate,'beginofapplysuminterval');
        }else{
            return $NewChargeRate;
        }
    }

    /**
    * 基金费率（新）
    * 该基金费率只返回无转入基金情况下的费率
    * @return array|boolean
    */
    public function chargeRateNew($fundCode, $chargeRateType)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        $sql = "SELECT * FROM MF_ChargeRateNew WHERE InnerCode=:InnerCode AND IfExecuted=1 AND ChargeRateCur=1420 AND LEFT (ChargeRateType,2)=:ChargeRateType ORDER BY StDivStand1 ASC";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $command->bindParam(":ChargeRateType", $chargeRateType);
        $ChargeRate = $command->queryAll();

        $NewChargeRate = [];
        if ($ChargeRate) {
            foreach ($ChargeRate as $key => $value) {
                $TmpChargeRate['excutedate'] = $value['ExcuteDate'] ? date("Y-m-d",strtotime($value['ExcuteDate'])) : '';
                $TmpChargeRate['minchargerate'] = round($value['MinChargeRate'],4);
                $TmpChargeRate['maxchargerate'] = round($value['MaxChargeRate'],4);
                $TmpChargeRate['chargeratetype'] = $value['ChargeRateType'] ?: '0';
                $TmpChargeRate['clienttype'] = $value['ClientType'] ?: '0';
                $TmpChargeRate['shiftintarget'] = $value['ShiftInTarget'] ?: '0';
                $TmpChargeRate['chargeratetydes'] = $value['ChargeRateTyDes'] ?: '';
                $TmpChargeRate['chargerateunit'] = $value['ChargeRateUnit'] ?: '';
                $TmpChargeRate['chargeratedes'] = $value['ChargeRateDes'] ?: '';
                $TmpChargeRate['divintervaldes'] = $value['DivIntervalDes'] ?: '';
                $TmpChargeRate['chargeratediv'] = $value['ChargeRateDiv'] ?: '';
                $TmpChargeRate['divstand1'] = $value['DivStand1'] ?: '';
                $TmpChargeRate['divstandunit1'] = $value['DivStandUnit1'] ?: '';
                $TmpChargeRate['stdivstand1'] = $value['StDivStand1'] ?: '';
                $TmpChargeRate['endivstand1'] = $value['EnDivStand1'] ?: '';
                $TmpChargeRate['ifapplystart1'] = $value['IfApplyStart1'] ?: '';
                $TmpChargeRate['ifapplyend1'] = $value['IfApplyEnd1'] ?: '';
                $TmpChargeRate['divstand2'] = $value['DivStand2'] ?: '';
                $TmpChargeRate['divstandunit2'] = $value['DivStandUnit2'] ?: '';
                $TmpChargeRate['stdivstand2'] = $value['StDivStand2'] ?: '';
                $TmpChargeRate['endivstand2'] = $value['EnDivStand2'] ?: '';
                $TmpChargeRate['ifapplystart2'] = $value['IfApplyStart2'] ?: '';
                $TmpChargeRate['ifapplyend2'] = $value['IfApplyEnd2'] ?: '';
                $TmpChargeRate['divstand3'] = $value['DivStand3'] ?: '';
                $TmpChargeRate['divstandunit3'] = $value['DivStandUnit3'] ?: '';
                $TmpChargeRate['stdivstand3'] = $value['StDivStand3'] ?: '';
                $TmpChargeRate['endivstand3'] = $value['EnDivStand3'] ?: '';
                $TmpChargeRate['ifapplystart3'] = $value['IfApplyStart3'] ?: '';
                $TmpChargeRate['ifapplyend3'] = $value['IfApplyEnd3'] ?: '';
                $TmpChargeRate['notes'] = $value['Notes'] ?: '';
                $NewChargeRate[] = $TmpChargeRate;
            }
        }
        
        return $NewChargeRate;
    }

    /**
    * 基金经理
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    public function fundManager($fundCode, $page=1, $pageSize=15)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 基金经理
        $FundManager = (new Query())
            ->select(['*'])
            ->from('MF_FundManager')
            ->where(['InnerCode' => $SecuMain['InnerCode']])
            ->all(self::getDb())
        ;

        $newFundManager = [];
        if ($FundManager) {
            foreach ($FundManager as $key => $value) {
                $tmpInterimBulletin['name'] = $value['Name'] ?: '';
                $tmpInterimBulletin['postname'] = $value['PostName'] ?: '';
                $tmpInterimBulletin['gender'] = $value['Gender'];
                $tmpInterimBulletin['birthdate'] = $value['BirthDate'] ? date("Y-m-d",strtotime($value['BirthDate'])) : '';
                $tmpInterimBulletin['educationlevel'] = $value['EducationLevel'] ?: '';
                $tmpInterimBulletin['practicedate'] = $value['PracticeDate'] ? date("Y-m-d",strtotime($value['PracticeDate'])) : '';
                $tmpInterimBulletin['background'] = $value['Background'] ?: '';
                $tmpInterimBulletin['incumbent'] = $value['Incumbent'];
                $tmpInterimBulletin['accessiondate'] = $value['AccessionDate'] ? date("Y-m-d",strtotime($value['AccessionDate'])) : '';
                $tmpInterimBulletin['dimissiondate'] = $value['DimissionDate'] ? date("Y-m-d",strtotime($value['DimissionDate'])) : '';
                $tmpInterimBulletin['performance'] = $value['Performance'] ?: '';
                $tmpInterimBulletin['notes'] = $value['Notes'] ?: '';
                $newFundManager[] = $tmpInterimBulletin;
            }
        }

        return $newFundManager;
    }

    /**
    * 净值走势（k线图）
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public function netValueChart($fundCode, $startDay)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // $sql = "SELECT * FROM MF_NetValuePerformanceHis WHERE InnerCode=:InnerCode AND TradingDay>=DATE_FORMAT(:startDay,'%Y-%m-%d') ORDER BY TradingDay DESC";
        $sql = "SELECT * FROM MF_NetValue WHERE InnerCode=:InnerCode AND EndDate>=DATE_FORMAT(:startDay,'%Y-%m-%d') ORDER BY EndDate DESC";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $command->bindParam(":startDay", $startDay);
        $netValueChart = $command->queryAll();

        $newNetValueChart = [];
        if ($netValueChart) {
            foreach ($netValueChart as $key => $value) {
                $tmpNetValueChart['tradingday'] = date("Y-m-d",strtotime($value['EndDate'])) ?: '';
                $tmpNetValueChart['unitnv'] = round($value['UnitNV'],4) ?: '';
                $tmpNetValueChart['dailyprofit'] = $value['DailyProfit'] ?: '';
                $tmpNetValueChart['latestweeklyyield'] = $value['LatestWeeklyYield'] ?: '';
                $newNetValueChart[] = $tmpNetValueChart;
            }
        }
        
        return $newNetValueChart;
    }

    /**
    * 同类均值（k线图）
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public function SimilarAvg($InnerCode, $startDay)
    {

    }

    /**
    * 沪深300（k线图）
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public function HS300($InnerCode, $startDay)
    {
        
    }

    /**
    * 历史净值
    * @param [string] 基金内部编码
    * @param [string] 起始时间
    * @return array|boolean
    */
    public function historyNetValue($fundCode, $startDay, $endDay)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
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


    /**
    * 基金概况
    * @param [string] 基金内部编码
    * @return array
    */
    public function fundArchives($fundCode)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 基金概况
        $FundArchive = (new Query())
            ->select(['*'])
            ->from('MF_FundArchives')
            ->where(['InnerCode' => $SecuMain['InnerCode']])
            ->one(self::getDb())
        ;

        $data = (new Query())
            ->from('fund_info')
            ->where(['FundCode'=>$fundCode])
            ->one(self::getDb('db_local'))
        ;

        $newFundArchive = [];
        $newFundArchive['fundtype'] = isset($data['FundType']) ? $data['FundType'] : '';
        $newFundArchive['foundedsize'] = isset($FundArchive['FoundedSize']) ? $FundArchive['FoundedSize'] : '';
        $newFundArchive['establishmentdate'] = isset($FundArchive['EstablishmentDate']) ? date("Y-m-d",strtotime($FundArchive['EstablishmentDate'])) : '';
        $newFundArchive['manager'] = isset($FundArchive['Manager']) ? $FundArchive['Manager'] : '';
        $newFundArchive['investtarget'] = isset($FundArchive['InvestTarget']) ? $FundArchive['InvestTarget'] : '';
        $newFundArchive['investorientation'] = isset($FundArchive['InvestOrientation']) ? $FundArchive['InvestOrientation'] : '';
        $newFundArchive['briefintro'] = isset($FundArchive['BriefIntro']) ? $FundArchive['BriefIntro'] : '';

        // 基金管理人
        $InvestAdvisorOutline = (new Query())
            ->select(['*'])
            ->from('MF_InvestAdvisorOutline')
            ->where(['InvestAdvisorCode' => isset($FundArchive['InvestAdvisorCode']) ? $FundArchive['InvestAdvisorCode'] : '0'])
            ->one(self::getDb())
        ;

        $newFundArchive['investadvisorname'] = isset($InvestAdvisorOutline['InvestAdvisorName']) ? $InvestAdvisorOutline['InvestAdvisorName'] : '';


        // 基金托管人
        $TrusteeOutline = (new Query())
            ->select(['*'])
            ->from('MF_TrusteeOutline')
            ->where(['TrusteeCode' => isset($FundArchive['TrusteeCode']) ? $FundArchive['TrusteeCode'] : '0'])
            ->one(self::getDb())
        ;

        $newFundArchive['trusteename'] = isset($TrusteeOutline['TrusteeName']) ? $TrusteeOutline['TrusteeName'] : '';

        return $newFundArchive;
    }

    
    /**
    * 投资组合(资产配置，重仓股)
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    public function investmentPortfolio($fundCode)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 资产配置
        $sql = "SELECT * FROM MF_AssetAllocation WHERE InnerCode=:InnerCode AND AssetTypeCode IN(10020, 10010, 10090, 1000202) AND ReportDate=(SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_AssetAllocation WHERE InnerCode=:InnerCode ORDER BY ReportDate DESC)";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $AssetAllocation = $command->queryAll();

        $NewAssetAllocation = [];
        if ($AssetAllocation) {
            foreach ($AssetAllocation as $key => $value) {
                $TmpAssetAllocation['marketvalue'] = $value['MarketValue'] ?: '';
                $TmpAssetAllocation['ratioinnv'] = $value['RatioInNV'] ?: '';
                $TmpAssetAllocation['assettype'] = $value['AssetType'] ?: '';
                $TmpAssetAllocation['reportdate'] = $value['ReportDate'] ?: '';
                $TmpAssetAllocation['infopubldate'] = $value['InfoPublDate'] ?: '';
                $NewAssetAllocation['assetallocation'][] = $TmpAssetAllocation;
            }
        }else{
            $NewAssetAllocation['assetallocation'] = [];
        }

        //行业配置
        $sql = "SELECT * FROM MF_InvestIndustry WHERE InnerCode=:InnerCode AND ReportDate=(SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_InvestIndustry WHERE InnerCode=:InnerCode ORDER BY ReportDate DESC)";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $InvestIndustry = $command->queryAll();

        $NewInvestIndustry = [];
        if ($InvestIndustry) {
            foreach ($InvestIndustry as $key => $value) {
                $TmpInvestIndustry['marketvalue'] = $value['MarketValue'] ?: '';
                $TmpInvestIndustry['ratioinnv'] = $value['RatioInNV'] ?: '';
                $TmpInvestIndustry['reportdate'] = $value['ReportDate'] ?: '';
                $TmpInvestIndustry['infopubldate'] = $value['InfoPublDate'] ?: '';
                $TmpInvestIndustry['industryname'] = $value['IndustryName'] ?: '';
				$TmpInvestIndustry['investtype'] = $value['InvestType'] ?: '';
                $NewInvestIndustry['investindustry'][] = $TmpInvestIndustry;
            }
        }else{
            $NewInvestIndustry['investindustry'] = [];
        }

        // 重仓股
        $sql = "SELECT * FROM MF_KeyStockPortfolio WHERE InnerCode=:InnerCode AND ReportDate=(SELECT MAX(ReportDate) AS MAX_ReportDate FROM MF_KeyStockPortfolio WHERE InnerCode=:InnerCode ORDER BY ReportDate DESC)";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $KeyStockPortfolio = $command->queryAll();

        $NewKeyStockPortfolio = [];
        if ($KeyStockPortfolio) {
            foreach ($KeyStockPortfolio as $key => $value) {
                $TmpKeyStockPortfolio['sharesholding'] = $value['SharesHolding'] ?: '';
                $TmpKeyStockPortfolio['marketvalue'] = $value['MarketValue'] ?: '';
                $TmpKeyStockPortfolio['ratioinnv'] = $value['RatioInNV'] ?: '';
                $TmpKeyStockPortfolio['infopubldate'] = $value['InfoPublDate'] ?: '';
                $TmpKeyStockPortfolio['reportdate'] = $value['ReportDate'] ?: '';
                $TmpKeyStockPortfolio['secuabbr'] = (new Query())
                    ->select(['SecuAbbr'])
                    ->from('SecuMain')
                    ->where(['InnerCode'=>$value['StockInnerCode']])
                    ->scalar(self::getDb())
                ;
                $NewKeyStockPortfolio['keystockportfolio'][] = $TmpKeyStockPortfolio;
            }
        }else{
            $NewKeyStockPortfolio['keystockportfolio'] = [];
        }
        
        return array_merge($NewAssetAllocation, $NewInvestIndustry, $NewKeyStockPortfolio);
    }
    
    /**
    * 基金公告（临时）
    * @param [string] 基金内部编码
    * @return array
    */
    public function interimBulletin($fundCode, $page=1, $pageSize=15)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 获取从表ID
        $sql = "SELECT ID FROM MF_InterimBulletin_SE WHERE CODE=:InnerCode";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $aID = $command->queryColumn();

        if ($aID) {
            $sID = '';
            foreach ($aID as $key => &$value) {
                $sID .= "'".$value."',";
            }
            $sID = substr($sID, 0, -1);
            $sID = "WHERE ID IN (".$sID.")";
        }else{
            $sID = '';
        }

        // 获取总记录数
        $sql = "SELECT COUNT(*) FROM MF_InterimBulletin ".$sID;
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $totalRecords = $command->queryScalar();
        // 分页参数判断
        $pageSize = $pageSize < 1 || $pageSize > $totalRecords ? 15 : $pageSize;
        $page = $page < 1 ? 1 : $page;
        if ($page > ceil($totalRecords / $pageSize)) {
            return [];
        }

        // 临时公告
        $sql = "SELECT * FROM MF_InterimBulletin ".$sID." ORDER BY BulletinDate DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $InterimBulletin = $command->queryAll();
        
        $newInterimBulletin = [];
        if ($InterimBulletin) {
            foreach ($InterimBulletin as $key => $value) {
                $tmpInterimBulletin['id'] = $value['ID'] ?: '';
                $tmpInterimBulletin['infotitle'] = $value['InfoTitle'] ?: '';
                $tmpInterimBulletin['detail'] = $value['Detail'] ?: '';
                $tmpInterimBulletin['bulletindate'] = $value['BulletinDate'] ? date("Y-m-d",strtotime($value['BulletinDate'])) : '';
                $newInterimBulletin[] = $tmpInterimBulletin;
            }

            $newInterimBulletin['totalrecords'] = $totalRecords;
        }

        return $newInterimBulletin;
    }

    /**
    * 基金公告（原文）
    * @param [string] 基金内部编码
    * @return array
    */
    public function interimAnnouncement($fundCode, $page=1, $pageSize=15)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 获取总记录数
        $sql = "SELECT COUNT(*) FROM MF_Announcement WHERE InnerCode=:InnerCode";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $totalRecords = $command->queryScalar();
        // 分页参数判断
        $pageSize = $pageSize < 1 || $pageSize > $totalRecords ? 15 : $pageSize;
        $page = $page < 1 ? 1 : $page;
        if ($page > ceil($totalRecords / $pageSize)) {
            return [];
        }

        // 原文公告
        $sql = "SELECT * FROM MF_Announcement WHERE InnerCode=:InnerCode ORDER BY InfoPublDate DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":InnerCode", $SecuMain['InnerCode']);
        $InterimBulletin = $command->queryAll();
        
        $newInterimBulletin = [];
        if ($InterimBulletin) {
            foreach ($InterimBulletin as $key => $value) {
                $tmpInterimBulletin['id'] = $value['ID'] ?: '';
                $tmpInterimBulletin['infotitle'] = $value['InfoTitle'] ?: '';
                $tmpInterimBulletin['detail'] = $value['Content'] ?: '';
                $tmpInterimBulletin['bulletindate'] = $value['InfoPublDate'] ? date("Y-m-d",strtotime($value['InfoPublDate'])) : '';
                $newInterimBulletin[] = $tmpInterimBulletin;
            }

            $newInterimBulletin['totalrecords'] = $totalRecords;
        }

        return $newInterimBulletin;
    }

    /**
    * 资产负债
    * @param [string] 基金内部编码
    * @return array|boolean
    */
    public function balanceSheet($fundCode)
    {
        // 基金主表数据
        $SecuMain = $this->getSecuMain($fundCode);

        if (!$SecuMain || empty($SecuMain)) {
            CommFun::handleCode('-100');
        }

        // 基金资产负债表
        $BalanceSheet = (new Query())
            ->select(['*'])
            ->from('MF_BalanceSheet')
            ->where(['InnerCode' => $SecuMain['InnerCode']])
            ->all(self::getDb())
        ;

        $newBalanceSheet = [];
        if ($BalanceSheet) {
            foreach ($BalanceSheet as $key => &$value) {
                $TmpBalanceSheet['reportdate'] = $value['ReportDate'] ?: '';
                $TmpBalanceSheet['deposit'] = $value['Deposit'] ?: '';
                $TmpBalanceSheet['settlementprovi'] = $value['Settlementprovi'] ?: '';
                $TmpBalanceSheet['dealcover'] = $value['DealCover'] ?: '';
                $TmpBalanceSheet['secusettlementreceivables'] = $value['SecuSettlementReceivables'] ?: '';
                $TmpBalanceSheet['dividendreceivables'] = $value['DividendReceivables'] ?: '';
                $TmpBalanceSheet['receivables'] = $value['Receivables'] ?: '';
                $TmpBalanceSheet['interestreceivables'] = $value['InterestReceivables'] ?: '';
                $TmpBalanceSheet['applyingreceivables'] = $value['ApplyingReceivables'] ?: '';
                $TmpBalanceSheet['otherreceivables'] = $value['OtherReceivables'] ?: '';
                $TmpBalanceSheet['stockoption'] = $value['StockOption'] ?: '';
                $TmpBalanceSheet['boughtsellbacksecu'] = $value['BoughtSellbackSecu'] ?: '';
                $TmpBalanceSheet['deferredexpense'] = $value['DeferredExpense'] ?: '';
                $TmpBalanceSheet['otherasset'] = $value['OtherAsset'] ?: '';
                $TmpBalanceSheet['totalasset'] = $value['TotalAsset'] ?: '';
                $TmpBalanceSheet['secusettlementpayables'] = $value['SecuSettlementPayables'] ?: '';
                $TmpBalanceSheet['redemptionmoneypayable'] = $value['RedemptionMoneyPayable'] ?: '';
                $TmpBalanceSheet['redemptionfeepayable'] = $value['RedemptionFeePayable'] ?: '';
                $TmpBalanceSheet['managementfeepayable'] = $value['ManagementFeePayable'] ?: '';
                $TmpBalanceSheet['trustfeepayable'] = $value['TrustFeePayable'] ?: '';
                $TmpBalanceSheet['performancepayment'] = $value['PerformancePayment'] ?: '';
                $TmpBalanceSheet['profitpayable'] = $value['ProfitPayable'] ?: '';
                $TmpBalanceSheet['accountpayable'] = $value['AccountPayable'] ?: '';
                $TmpBalanceSheet['commisionpayable'] = $value['CommisionPayable'] ?: '';
                $TmpBalanceSheet['allocationfundpayable'] = $value['AllocationFundPayable'] ?: '';
                $TmpBalanceSheet['interestpayable'] = $value['InterestPayable'] ?: '';
                $TmpBalanceSheet['salefeepayable'] = $value['SaleFeePayable'] ?: '';
                $TmpBalanceSheet['otherpayable'] = $value['OtherPayable'] ?: '';
                $TmpBalanceSheet['soldreposecuproceeds'] = $value['SoldRepoSecuProceeds'] ?: '';
                $TmpBalanceSheet['otherdebts'] = $value['OtherDebts'] ?: '';
                $TmpBalanceSheet['totalliability'] = $value['TotalLiability'] ?: '';
                $TmpBalanceSheet['capital'] = $value['Capital'] ?: '';
                $TmpBalanceSheet['unrealizedprofit'] = $value['UnrealizedProfit'] ?: '';
                $TmpBalanceSheet['retainedprofit'] = $value['RetainedProfit'] ?: '';
                $TmpBalanceSheet['otherequity'] = $value['OtherEquity'] ?: '';
                $TmpBalanceSheet['totalshareholderequity'] = $value['TotalShareHolderEquity'] ?: '';
                $TmpBalanceSheet['totalliabilityandequity'] = $value['TotalLiabilityAndEquity'] ?: '';
                $newBalanceSheet[] = $TmpBalanceSheet;
            }
        }
        
        return $newBalanceSheet;
    }
}
