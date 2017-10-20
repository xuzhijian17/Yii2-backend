<?php
namespace console\commands;

use Yii;

class FundMarketController extends \yii\console\Controller
{
	public $sqls = [];

	public function actionIndex($value='')
	{
		$sql = "SELECT s.InnerCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType FROM `SecuMain` s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8";
		$connection = Yii::$app->db_juyuan;
		$command = $connection->createCommand($sql);
        $fundData = $command->query();

        foreach ($fundData as $key => $value) {
        	$sql = "INSERT INTO fund_market(InnerCode,SecuCode,ChiSpelling,SecuAbbr,FundType,FundTypeCode,InvestmentType,SysTime) VALUES(".$value['InnerCode'].",'".$value['SecuCode']."','".$value['ChiSpelling']."','".$value['SecuAbbr']."','".$value['FundType']."',".$value['FundTypeCode'].",".$value['InvestmentType'].",".time().")";
        	$connection = Yii::$app->db;
			$command = $connection->createCommand($sql);
			$rs = $command->execute();
			echo $rs;
        }
        
	}

	public function actionSyncFundData($value='')
	{
		// 使用晨星分类
		$addSqls = [
			'AND fa.FundTypeCode=1101',		// 股票型
			'AND fa.FundTypeCode=1103',		// 混合型
			'AND fa.FundTypeCode=1105 AND fa.InvestStyle!=8',	// 债券型（不包含短期债券）
			'AND fa.FundTypeCode=1107',		// 保本型
			'AND fa.FundTypeCode=1199',		// 其它型
			'AND fa.InvestmentType=7'	// 指数型
		];
        foreach ($addSqls as $key => $addSql) {
        	$this->sqls[] = "SELECT s.InnerCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType,fa.InvestStyle,fa.InvestAdvisorCode,iao.InvestAdvisorAbbrName,n.UnitNV,n.NVDailyGrowthRate,n.RRInSingleWeek,n.RRInSingleMonth,n.RRInThreeMonth,n.RRInSixMonth,n.RRInSingleYear,n.RRSinceThisYear,n.TradingDay FROM SecuMain s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 ".$addSql." LEFT JOIN MF_InvestAdvisorOutline iao ON fa.InvestAdvisorCode=iao.InvestAdvisorCode LEFT JOIN MF_NetValuePerformance n ON s.InnerCode=n.InnerCode WHERE n.UnitNV IS NOT NULL";
        }
		
		// 货币型基金或理财型（短期债券）,使用晨星分类
		$addSqls = [
			'AND fa.FundTypeCode=1109',		// 货币型
			'AND fa.InvestStyle=8',		// 理财型（短期债券）
		];
		foreach ($addSqls as $key => $value) {
			$this->sqls[] = "SELECT s.InnerCode,s.SecuCode,s.ChiSpelling,s.SecuAbbr,fa.FundType,fa.FundTypeCode,fa.InvestmentType,fa.InvestStyle,fa.FundNature,iao.InvestAdvisorAbbrName,nv.DailyProfit,nv.LatestWeeklyYield,nv.TradingDay FROM `SecuMain` s INNER JOIN MF_FundArchives fa ON s.InnerCode=fa.InnerCode AND s.SecuCategory=8 ".$addSql." LEFT JOIN MF_InvestAdvisorOutline iao ON fa.InvestAdvisorCode=iao.InvestAdvisorCode, MF_NetValue nv WHERE s.InnerCode=nv.InnerCode AND nv.EndDate=(SELECT MAX(EndDate) FROM MF_NetValue WHERE InnerCode=s.InnerCode) AND nv.DailyProfit IS NOT NULL AND nv.LatestWeeklyYield IS NOT NULL ORDER BY nv.LatestWeeklyYield DESC";
		}

		foreach ($this->sqls as $sql) {
			$connection = Yii::$app->db_juyuan;
			$command = $connection->createCommand($sql);
	        $fundData = $command->query();

	        foreach ($fundData as $key => $value) {
	        	if (!$value) {
					continue;
				}

				foreach ($value as $k => &$v) {
					if ($v === null) {
						$v = 0;
					}
				}

				if ($value['FundTypeCode'] == 1109 || $value['InvestStyle'] == 8) {
					$sql = "UPDATE fund_market SET FundType='".$value['FundType']."',FundTypeCode=".$value['FundTypeCode'].",DailyProfit=".$value['DailyProfit'].",LatestWeeklyYield=".$value['LatestWeeklyYield'].",Company='".$value['InvestAdvisorAbbrName']."' WHERE InnerCode=".$value['InnerCode'];
				}else{
					$sql = "UPDATE fund_market SET FundType='".$value['FundType']."',FundTypeCode=".$value['FundTypeCode'].",UnitNV=".$value['UnitNV'].",NVDailyGrowthRate=".$value['NVDailyGrowthRate'].",RRInSingleWeek=".$value['RRInSingleWeek'].",RRInSingleMonth=".$value['RRInSingleMonth'].",RRInThreeMonth=".$value['RRInThreeMonth'].",RRInSixMonth=".$value['RRInSixMonth'].",RRInSingleYear=".$value['RRInSingleYear'].",RRSinceThisYear=".$value['RRSinceThisYear'].",Company='".$value['InvestAdvisorAbbrName']."' WHERE InnerCode=".$value['InnerCode'];
				}
	        	
	        	
				$connection = Yii::$app->db;
				$command = $connection->createCommand($sql);
				$rs = $command->execute();

				if ($rs === false) {
					echo $sql."\r\n";
					Yii::error("This sql `{$sql}` has an error.");
				}else{
					echo "同步更新聚源{$value['FundType']}基金数据成功\r\n";
				}
	        }	        
		}
		echo "================================================================================== Done. -- ".date("Y-m-d");
		Yii::info(date("Y-m-d H:i:s")." -- sync fund data done.");
	}
}