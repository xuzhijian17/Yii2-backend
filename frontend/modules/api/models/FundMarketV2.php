<?php

namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\HundSun;
use common\lib\CommFun;
use frontend\services\TradeService;

class FundMarketV2 extends Model
{

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
     * 获取基金列表
     * @param $id Type id，maybe is ThemeId or CategoryId, according to type field.
     * @param $type 1-theme 2-fundtype 3-hot
     */
    public function getCFundList($instid, $cid, $tid, $hot, $page=1, $pageSize=15)
    {
        // 初始sql
        $sql = " FROM fund_list_{$instid} fl ".($cid?"RIGHT":"LEFT")." JOIN fund_category fc ON fl.CategoryId=fc.id ".($cid?" AND fc.id={$cid} ":"")." ".($tid?"RIGHT":"LEFT")." JOIN fund_theme ft ON fl.ThemeId=ft.id ".($tid?" AND ft.id={$tid} ":"")." WHERE fl.`Status`=0";
        /*$sql = " FROM fund_list_{$instid} fl ";

        if ($tid) {
            $sql .= "LEFT JOIN fund_theme ft ON fl.ThemeId=ft.id AND ft.`Status`!=-1 WHERE fl.`Status`=0 AND ft.id={$tid}";
        }elseif ($cid) {
            $sql .= "LEFT JOIN fund_category fc ON fl.CategoryId=fc.id AND fc.`Status`!=-1 WHERE fl.`Status`=0 AND fc.id={$cid}";
        }else{
            $sql .= "WHERE fl.Status = 0";
        }*/

        // 计算满足查询条件的总记录数（得在分页sql前）
        $sqlCount = "SELECT COUNT(*)".$sql;
        $command = self::getDb()->createCommand($sqlCount);
        $totalRecords = $command->queryScalar();
        
        // 分页参数
        // $sql .= " ORDER BY fl.IsTop DESC, fl.UpdateTime DESC LIMIT ".(($page - 1) * $pageSize).",".$pageSize;
        $sql .= " ORDER BY fl.InsertTime DESC";

        // 查询列表数据
        $sqlList = "SELECT *,fl.id,fl.UpdateTime,fl.InsertTime".$sql;
        $command = self::getDb()->createCommand($sqlList);
        $data = $command->queryAll();
        
        $fundList = [];
        if ($data) {
            foreach ($data as $key => &$value) {
                $tmpArr['category'] = isset($value['Category']) ? $value['Category'] : '';
                $tmpArr['fundcode'] = $value['FundCode'];
                $tmpArr['fundname'] = $value['FundName'];
                $tmpArr['tags'] = $value['Tags'];
                $tmpArr['istop'] = $value['IsTop'];
                $tmpArr['recommend'] = $value['Recommend'];
                $tmpArr['updatetime'] = $value['UpdateTime'];
                $tmpArr['inserttime'] = $value['InsertTime'];
                $fundList[] = $tmpArr;
            }

            // 列表分页附加数据
            $data['totalRecords'] = $totalRecords;
            $data['totalPages'] = ceil($data['totalRecords']/$pageSize); 
            $data['page'] = $page;
        }
        
        return $fundList;
    }

    /**
     * 自定义基金列表
     * @return array
     */
    public function getFundList($instid, $cid, $tid, $hot, $page=1, $pageSize=15)
    {        
        $sql = "SELECT fl.id,fc.id AS cid,ft.id AS tid,fl.FundCode,fl.FundName,fl.CategoryId,fc.Category,fl.Recommend,ft.Theme,fl.InsertTime FROM fund_list_{$instid} fl ".($cid?" RIGHT ":"LEFT")." JOIN fund_category fc ON fl.CategoryId=fc.id ".($cid?" AND fc.id={$cid} ":"")." ".($tid?" RIGHT ":"LEFT")." JOIN fund_theme ft ON fl.ThemeId=ft.id ".($tid?" AND ft.id={$tid} ":"")." WHERE fl.`Status`!=-1";
        $command = self::getDb('db_local')->createCommand($sql);
        $fundData = $command->queryAll();

        return $this->fundList($fundData);
    }

    /**
     * 获取基金分类
     * @return array
     */
    public function getFundCats($instid)
    {
        $rs = [];
        
        $sql = "SELECT * FROM fund_category WHERE Instid=:instid AND `Status`!=-1";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":instid", $instid);
        $fundCats = $command->queryAll();
        
        if ($fundCats) {
            foreach ($fundCats as $key => $value) {
                $tmpArr['id'] = isset($value['id']) ? $value['id'] : 0;
                $tmpArr['category'] = isset($value['Category']) ? $value['Category'] : '';

                $rs[] = $tmpArr;
            }
        }

        return $rs;
    }

    /**
     * 获取主题分类
     * @return array
     */
    public function getFundThemes($merid)
    {
        $rs = [];

        $sql = "SELECT * FROM fund_theme WHERE Instid=:merid";
        $command = self::getDb()->createCommand($sql);
        $command->bindParam(":merid", $merid);
        $fundThemes = $command->queryAll();

        if ($fundThemes) {
            foreach ($fundThemes as $key => $value) {
                $tmpArr['id'] = $value['id'] ?: '0';
                $tmpArr['theme'] = $value['Theme'] ?: '';
                $tmpArr['describe'] = $value['Describe'] ?: '';
                $tmpArr['image'] = $value['Image'] ?: '';

                $rs[] = $tmpArr;
            }
        }

        return $rs;
    }

    /**
     * 获取基金列表
     * @return array
     */
    public function fundList($fundData)
    {
        $fundList = [];

        if (!$fundData) {
            return [];
        }
        
        foreach ($fundData as $key => $value) {
            if (!$value['FundCode']) {
                continue;
            }

            // 获取聚源数据中的基金类型代码
            $sql = "SELECT fa.FundTypeCode,fa.InvestStyle FROM SecuMain s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 AND s.SecuCode=:secuCode";
            $command = self::getDb('db_juyuan')->createCommand($sql);
            $command->bindParam(":secuCode", $value['FundCode']);
            $FundType = $command->queryOne();

            if ($FundType['FundTypeCode'] == 1109 || $FundType['InvestStyle'] == 8) {
                // 货币型基金或短期理财型基金
                // $sql = "SELECT * FROM (SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType,fa.FundNature,nv.DailyProfit,nv.LatestWeeklyYield FROM `SecuMain` s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 AND (fa.FundTypeCode=1109 OR fa.InvestStyle=8) AND s.SecuCode=:secuCode, MF_NetValue nv WHERE s.InnerCode=nv.InnerCode AND nv.DailyProfit IS NOT NULL AND nv.LatestWeeklyYield IS NOT NULL ORDER BY nv.EndDate DESC) a GROUP BY a.InnerCode ORDER BY a.LatestWeeklyYield DESC";
                $sql = "SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType,fa.InvestStyle,fa.FundNature,nv.DailyProfit,nv.LatestWeeklyYield FROM `SecuMain` s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 AND (fa.FundTypeCode = 1109 OR fa.InvestStyle = 8) LEFT JOIN MF_MMYieldPerformance nv ON s.InnerCode=nv.InnerCode AND nv.TradingDay=(SELECT MAX(TradingDay) FROM MF_MMYieldPerformance WHERE InnerCode=s.InnerCode) AND nv.DailyProfit IS NOT NULL AND nv.LatestWeeklyYield IS NOT NULL ORDER BY nv.LatestWeeklyYield DESC";
            }else{
                // 非货币型基金和非短期理财债券
                $sql = "SELECT s.InnerCode,s.CompanyCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType,fa.FundNature,n.UnitNV,n.NVDailyGrowthRate,n.RRInSingleWeek,n.RRInSingleMonth,n.RRInThreeMonth,n.RRInSixMonth,n.RRInSingleYear,n.RRSinceThisYear FROM SecuMain s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 AND fa.FundTypeCode!=1109 AND fa.InvestStyle!=8 AND s.SecuCode=:secuCode LEFT JOIN MF_NetValuePerformance n ON s.InnerCode=n.InnerCode ORDER BY n.NVDailyGrowthRate DESC";
            }

            $command = self::getDb('db_juyuan')->createCommand($sql);
            $command->bindParam(":secuCode", $value['FundCode']);
            $fundData = $command->queryOne();
            
            // 返回数据字段及格式转换
            $tmpArr['id'] = $value['id'] ?: '0';
            $tmpArr['cid'] = $value['cid'] ?: '0';
            $tmpArr['category'] = $value['Category'] ?: '';
            $tmpArr['tid'] = $value['tid'] ?: '0';
            $tmpArr['theme'] = $value['Theme'] ?: '';
            $tmpArr['fundtype'] = $fundData['FundType'];
            $tmpArr['fundcode'] = $value['FundCode'] ?: $fundData['SecuCode'];
            $tmpArr['fundname'] = $value['FundName'] ?: $fundData['SecuAbbr'];
            $tmpArr['fundnature'] = $fundData['FundNature'];
            $tmpArr['unitnv'] = isset($value['UnitNV']) ? sprintf('%.4f',$value['UnitNV']) : sprintf('%.2f',0);
            $tmpArr['dailyprofit'] = isset($value['DailyProfit']) ? sprintf('%.4f',$value['DailyProfit']) : sprintf('%.2f',0);  // 万份收益
            $tmpArr['latestweeklyyield'] = isset($value['LatestWeeklyYield']) ? sprintf('%.4f',$value['LatestWeeklyYield']) : sprintf('%.2f',0);    // 七日年化收益
            $tmpArr['dailygrowth'] = isset($value['NVDailyGrowthRate']) ? sprintf('%.2f',$value['NVDailyGrowthRate']) : sprintf('%.2f',0);
            $tmpArr['insingleweek'] = isset($value['RRInSingleWeek']) ? sprintf('%.2f',$value['RRInSingleWeek']) : sprintf('%.2f',0);
            $tmpArr['insinglemonth'] = isset($value['RRInSingleMonth']) ? sprintf('%.2f',$value['RRInSingleMonth']) : sprintf('%.2f',0);
            $tmpArr['inthreemonth'] = isset($value['RRInThreeMonth']) ? sprintf('%.2f',$value['RRInThreeMonth']) : sprintf('%.2f',0);
            $tmpArr['insixmonth'] = isset($value['RRInSixMonth']) ? sprintf('%.2f',$value['RRInSixMonth']) : sprintf('%.2f',0);
            $tmpArr['insingleyear'] = isset($value['RRInSingleYear']) ? sprintf('%.2f',$value['RRInSingleYear']) : sprintf('%.2f',0);
            $tmpArr['inserttime'] = $value['InsertTime'] ?: '';

            $fundList[] = $tmpArr;
        }
        
        return $fundList;
    }
}
