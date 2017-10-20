<?php

namespace frontend\models;

use Yii;
use yii\db\Query;

/**
 * Query represents a SELECT SQL statement in a way that is independent of DBMS.
 *
 */
class FundQuery extends Query
{
    /**
    * Define fund category
    */
    const SECUCATEGORY = 8;

    /**
    * The following belongs to the classification standard
    */
    const STOCK_TYPE_CODE = 1101;

    const MIX_TYPE_CODE = 1103;
    
    const BOND_TYPE_CODE = 1105;

    const BREAKEVEN_TYPE_CODE = 1107;

    const CURRENCY_TYPE_CODE = 1109;

    const OTHER_TYPE_CODE = 1199;

    /**
    * The following belongs to the Investment Type
    */
    const INDEX_TYPE_CODE = 7;

    /**
    * The following belongs to the Invest Style
    */
    const MONEY_TYPE_CODE = 8;


    /**
    * Fund list data
    */
    public static $fundData = [];

    public $db;

    /**
    * The juyuan database connection instance
    */
    public static function getDb($db_name='')
    {
        return Yii::$app->db_juyuan;
    }

    /**
    * The redis database connection instance
    */
    public static function getRedis()
    {
        return Yii::$app->redis;
    }


    public static function multi_array_sort(&$multi_array,$sort_key,$sort=SORT_DESC){ 
        if(is_array($multi_array)){
            $key_array = array();
            foreach ($multi_array as $row_array){ 
                if(is_array($row_array)){ 
                    $key_array[] = $row_array[$sort_key]; 
                }else{ 
                    return false; 
                } 
            }
            array_multisort($key_array,$sort,$multi_array); 
        }else{ 
            return false; 
        }
        
        return $multi_array; 
    } 


    /**
    * The fund list
    * @param [string] fund type
    * @param [string] yield sort
    * @param [int] page 
    * @param [int] page size
    * @return fund list of json data
    */
    public static function fundList($fundType, $yieldType='NVDailyGrowthRate', $page=1, $pageSize=15)
    {
        // Judge fund type
        if ($fundType === 'stock') {
            $addSql = "AND fa.FundTypeCode=".self::STOCK_TYPE_CODE;
        }elseif ($fundType === 'mix') {
           $addSql = "AND fa.FundTypeCode=".self::MIX_TYPE_CODE;
        }elseif ($fundType === 'index') {
            $addSql = "AND fa.InvestmentType=".self::INDEX_TYPE_CODE;    // 指数类型使用InvestmentType字段
        }elseif ($fundType === 'bond') {
            $addSql = "AND fa.FundTypeCode=".self::BOND_TYPE_CODE;
        }elseif ($fundType === 'currency') {
            $addSql = "AND fa.FundTypeCode=".self::CURRENCY_TYPE_CODE;
            /*if (unserialize(self::getRedis()->executeCommand('HGET',['fundData', 'date'])) != date("Y-m-d")) {
                self::getRedis()->executeCommand('HSET',['fundData', 'date', serialize(date("Y-m-d"))]);
            }else{
                if (self::getRedis()->executeCommand('EXISTS',['fundData']) && unserialize(self::getRedis()->executeCommand('HGET',['fundData', 'date'])) == date("Y-m-d") && $page == 1) {
                    self::$fundData = unserialize(self::getRedis()->executeCommand('HGET',['fundData','currencyList']));
                    return self::$fundData;
                }
            }*/
        }elseif ($fundType === 'money') {
            $addSql = "AND faa.TypeCode=10 AND faa.DataCode=1106";    // 理财类型使用证监会基金分类中的短期理财债券型
        }elseif ($fundType === 'breakeven') {
            $addSql = "AND fa.FundTypeCode=".self::BREAKEVEN_TYPE_CODE;
        }

        // Init sql statement
        if ($fundType === 'currency') {
            // 货币型基金，取万份收益和7日年化收益
            // $sql = "SELECT * FROM (SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundTypeCode,nv.DailyProfit,nv.LatestWeeklyYield FROM `SecuMain` s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 ".$addSql.", MF_NetValue nv WHERE s.InnerCode=nv.InnerCode AND nv.DailyProfit IS NOT NULL AND nv.LatestWeeklyYield IS NOT NULL ORDER BY nv.EndDate DESC) a GROUP BY a.InnerCode ORDER BY a.LatestWeeklyYield DESC,a.SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
            $sql = "SELECT * FROM fund_market WHERE FundTypeCode=1109 ORDER BY LatestWeeklyYield DESC,SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        }elseif ($fundType === 'money') {
            // 理财型基金，取万份收益和7日年化收益，使用证监会基金分类中的短期理财债券型
            // $sql = "SELECT * FROM (SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,faa.TypeCode,faa.DataCode AS FundTypeCode,nv.DailyProfit,nv.LatestWeeklyYield FROM `SecuMain` s LEFT JOIN MF_NetValue nv ON s.InnerCode=nv.InnerCode AND s.SecuCategory=8, MF_FundArchivesAttach faa WHERE faa.InnerCode=s.InnerCode AND nv.DailyProfit IS NOT NULL AND nv.LatestWeeklyYield IS NOT NULL ".$addSql." ORDER BY nv.EndDate DESC) a GROUP BY a.InnerCode ORDER BY a.LatestWeeklyYield DESC,a.SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
            $sql = "SELECT * FROM fund_market WHERE FundTypeCode=1106 ORDER BY LatestWeeklyYield DESC,SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        }else{
            // Query SecuMain,MF_FundArchives,MF_NetValuePerformance table
            // $sql = "SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundTypeCode,n.UnitNV,n.NVDailyGrowthRate,n.RRInSingleWeek,n.RRInSingleMonth,n.RRInThreeMonth,n.RRInSixMonth,n.RRInSingleYear,n.RRSinceThisYear FROM SecuMain s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 ".$addSql." LEFT JOIN MF_NetValuePerformance n ON s.InnerCode=n.InnerCode WHERE n.UnitNV IS NOT NULL ORDER BY n.".$yieldType." DESC,s.SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
            $sql = "SELECT * FROM fund_market fa WHERE fa.UnitNV IS NOT NULL ".$addSql." ORDER BY fa.".$yieldType." DESC,fa.SecuCode DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        }

        // $command = self::getDb()->createCommand($sql);
        $command = Yii::$app->db->createCommand($sql);
        $fundData = $command->queryAll();

        foreach ($fundData as $key => &$value) {
            // Format fund data style
            // $value['TypeCode'] = (isset($value['TypeCode']) ? $value['TypeCode'] : 0);
            // $value['DataCode'] = (isset($value['DataCode']) ? $value['DataCode'] : 0);
            $value['FundTypeCode'] = (isset($value['FundTypeCode']) ? $value['FundTypeCode'] : 0);
            $value['UnitNV'] = (isset($value['UnitNV']) ? sprintf('%1.4f',$value['UnitNV']) : sprintf('%1.2f',0));
            $value['NVDailyGrowthRate'] = (isset($value['NVDailyGrowthRate']) ? sprintf('%1.2f',$value['NVDailyGrowthRate']) : sprintf('%1.2f',0));
            $value['RRInSingleWeek'] = (isset($value['RRInSingleWeek']) ? sprintf('%1.2f',$value['RRInSingleWeek']) : sprintf('%1.2f',0));
            $value['RRInSingleMonth'] = (isset($value['RRInSingleMonth']) ? sprintf('%1.2f',$value['RRInSingleMonth']) : sprintf('%1.2f',0));
            $value['RRInThreeMonth'] = (isset($value['RRInThreeMonth']) ? sprintf('%1.2f',$value['RRInThreeMonth']) : sprintf('%1.2f',0));
            $value['RRInSixMonth'] = (isset($value['RRInSixMonth']) ? sprintf('%1.2f',$value['RRInSixMonth']) : sprintf('%1.2f',0));
            $value['RRInSingleYear'] = (isset($value['RRInSingleYear']) ? sprintf('%1.2f',$value['RRInSingleYear']) : sprintf('%1.2f',0));
            $value['RRSinceThisYear'] = (isset($value['RRSinceThisYear']) ? sprintf('%1.2f',$value['RRSinceThisYear']) : sprintf('%1.2f',0));
            $value['DailyProfit'] = (isset($value['DailyProfit']) ? sprintf('%1.4f',$value['DailyProfit']) : sprintf('%1.2f',0));
            $value['LatestWeeklyYield'] = (isset($value['LatestWeeklyYield']) ? sprintf('%1.2f',$value['LatestWeeklyYield']) : sprintf('%1.2f',0));
        }

        if ($fundType === 'currency' && $page == 1) {
            $currencyList = self::getRedis()->executeCommand('HSET',['fundData', 'currencyList', serialize($fundData)]);
            if (!$currencyList) {
                Yii::error("Insert fund list to redis failed: ", __METHOD__);
            }
        }
        
        return $fundData;
    }

    /**
    * Fund search function, the keyword can enter fund code, fund name for short or fund Chinese Spelling
    * @param [string] search keyword
    * @return search result of json data
    */
    public static function search($s)
    {
        $sql = "SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,a.FundType,a.FundTypeCode,a.InvestmentType,a.InvestStyle FROM SecuMain s INNER JOIN MF_FundArchives a ON s.InnerCode=a.InnerCode WHERE s.SecuCategory=8 AND (s.SecuCode LIKE '".$s."%' OR s.SecuAbbr LIKE '".$s."%' OR s.ChiSpelling LIKE '".$s."%') LIMIT 100";

        $command = self::getDb()->createCommand($sql);
        $rs = $command->queryAll();

        return $rs;
    }
}
