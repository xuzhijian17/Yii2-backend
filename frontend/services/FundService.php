<?php
namespace frontend\services;
use Yii;
use frontend\models\SecuMain;
use frontend\models\NetValuePerformance;
use frontend\models\SystemConst;
use frontend\models\NetValuePerformanceHis;
use frontend\models\FundArchives;
use frontend\models\FundArchivesAttach;
use frontend\models\FundRating;
use frontend\models\FundManager;
use frontend\models\NetValue;
use frontend\models\BenchmarkGrowthRate;
use frontend\models\TrusteeOutline;
use frontend\models\InvestAdvisorOutline;
use frontend\models\MainFinancialIndexQ;
use frontend\models\InvestIndustry;
use frontend\models\ChargeRate;
use frontend\models\KeyStockPortfolio;
use frontend\models\BondPortifolioStru;
use frontend\models\HsStock;
class FundService extends Service
{
	//基础信息
	public static function getFundBase($InnerCode)
	{
		$data = [];
		//基金基础信息
		$data = SecuMain::find($InnerCode);
		if(!$data) return false;
		$FundArchives = FundArchives::find($InnerCode);
		$FundArchivesAttach = FundArchivesAttach::find($InnerCode);
		//$FundType = SystemConst::getFundType(); //基金类型
		$data['MS'] = $FundArchivesAttach['DataCode'] == 1106 ? '理财型' : $FundArchivesAttach['DataName'];// 基金类型（股票、债券）
		$data['MSType'] = $FundArchivesAttach['DataCode'];// 基金类型（股票、债券）
		$data['FundTypeCode'] = $FundArchivesAttach['DataCode'];
		
		return $data;
	}
	//基金基础信息
	public static function getFundDetails($InnerCode)
	{
		//基金概况
		$FundArchives = FundArchives::find($InnerCode);
		$FundArchivesAttach = FundArchivesAttach::find($InnerCode);
		$data['Manager'] = $FundArchives['Manager'];
		//1109 货币 1106 理财
		if(in_array($FundArchivesAttach['DataCode'], [1109,1106])){
			//货币基金最新净值
			$NetValue = NetValue::find($InnerCode);
		}else{
			//基金最新净值
			$NetValuePerformance = NetValuePerformance::find($InnerCode);
		}
		if(in_array($FundArchivesAttach['DataCode'],[1109, 1106])){
			$data['UnitNV'] = sprintf("%.4f", round($NetValue['LatestWeeklyYield']*100, 4), 4);
			$data['NVDailyGrowthRate'] = sprintf("%.4f", round($NetValue['DailyProfit'], 4), 4);
			$data['UpdateTime'] = substr($NetValue['EndDate'], 5, 5);
		}else{
			$data['UnitNV'] = $NetValuePerformance['UnitNV'];
			$data['UpdateTime'] = substr($NetValuePerformance['UpdateTime'], 5, 5);
			$data['NVDailyGrowthRate'] = sprintf("%.2f", round($NetValuePerformance['NVDailyGrowthRate'], 2), 2);
		}
		//评级
		$FundRating = FundRating::find($InnerCode);
		$RiskEvaluation = SystemConst::getRiskEvaluation(); //基金评级
		$StarRank = SystemConst::getStarRank();//基金星级
		$data['RiskEvaluation'] = isset($FundRating['RiskEvaluation']) && $FundRating['RiskEvaluation'] ? $RiskEvaluation[$FundRating['RiskEvaluation']]['MS'] : '低';
		$data['RiskEvaluationDM'] = isset($FundRating['RiskEvaluation']) && $FundRating['RiskEvaluation'] ? $RiskEvaluation[$FundRating['RiskEvaluation']]['DM'] : 1;
		$data['StarRank'] = isset($FundRating['StarRank']) ? $StarRank[$FundRating['StarRank']]['DM'] : 0;// 基金评级
		$data['StarRank'] = $data['StarRank'] == 6 ? 5 : ($data['StarRank'] == 99 ? 0 : $data['StarRank']);
		return $data;
	}
	
	//基金分时图数据
	public static function getFundMarket($InnerCode, $type = 1, $typeCode)
	{
		if(!in_array($typeCode, [1106, 1109])){
			$fundLine = NetValuePerformanceHis::getFundLine($InnerCode, $type);
			//print_r($fundLine);
			$line= NetValuePerformanceHis::getFundGroupLine($InnerCode, $type, $typeCode);
			//$hs = HsStock::getFundLine('399300', $type);
			foreach($fundLine as $key=>&$val){
				$val = array_merge($line[$key], /*$hs[$key],*/ $val);
			}
			//print_r($fundLine);
		}else{
			$fundLine = NetValue::getFundLine($InnerCode, $type);
		}
		// $bool = in_array($this->base['FundTypeCode'], [1106, 1109]) ? false : true;
		// $fundLine = $bool ? NetValuePerformanceHis::getFundLine($InnerCode, $type) :
						// NetValue::getFundLine($InnerCode, $type);
		return $fundLine;
	}
	
	//基金表现
	public static function getFundResultSort($InnerCode, $fundType, $type = null)
	{
		$result = in_array($fundType, [1109, 1106]) ? BenchmarkGrowthRate::getFundResult($fundType, $InnerCode) :
					NetValuePerformance::getFundResult($fundType);
		//return $result;
		$typeField = ['RRInSingleWeek' => '近一周', 'RRInSingleMonth' => '近一月', 
				'RRInThreeMonth' => '近三月', 'RRInSixMonth' => '近半年', 
				'RRInSingleYear' => '近一年', 'RRSinceThisYear' => '今年来'];
		$data = []; 
		if($type != null){
			$sort = NetValuePerformance::getFundResultSort($result, array_keys($typeField)[$type]);
			return ['RRIn' => $sort[$InnerCode][array_keys($typeField)[$type]], 'sort'=>$sort[$InnerCode]['sort'], 'total' => count($sort), 'desc' => $typeField[array_keys($typeField)[$type]]];
		}
		//echo $InnerCode;
		foreach($typeField as $key=>$val){
			$sort = NetValuePerformance::getFundResultSort($result, $key);
			//print_r($sort[$InnerCode]);
			$data[] = !isset($sort[$InnerCode]) ? ['RRIn' => '-', 'sort'=>0, 'total' => 0, 'desc' => $val] :
					['RRIn' => round($sort[$InnerCode][$key], 2), 'sort'=>$sort[$InnerCode]['sort'], 'total' => count($sort), 'desc' => $val];
		}
		return $data;
	}
	
	
	//基金概况
	public static function getFundProfile($InnerCode)
	{
		$data = [];
		//基金基础信息
		$SecuMain = SecuMain::find($InnerCode);
		$data['ChiName'] = $SecuMain['ChiNameAbbr'];
		//基金概况
		$FundArchives = FundArchives::find($InnerCode);
		$InvestAdvisorOutline = InvestAdvisorOutline::find($FundArchives['InvestAdvisorCode']);
		$TrusteeOutline = TrusteeOutline::find($FundArchives['TrusteeCode']);
		$MainFinancialIndexQ = MainFinancialIndexQ::find($InnerCode);
		$data['EstablishmentDate'] = $FundArchives['EstablishmentDate'];//成立日期
		$data['ListedDate'] = $FundArchives['ListedDate'];//上市日期
		$data['NetAssetsValue'] = round($MainFinancialIndexQ['NetAssetsValue']/100000000, 2);//资金规模
		$data['InvestAdvisorName'] = $InvestAdvisorOutline['InvestAdvisorName'];//基金管理人
		$data['TrusteeName'] = $TrusteeOutline['TrusteeName'];//基金托管人
		$data['InvestOrientation'] = $FundArchives['InvestOrientation'];//策略
		$data['InvestTarget'] = $FundArchives['InvestTarget'];//理念
		//基金经理
		$FundManager = FundManager::query($InnerCode);
		//var_dump($FundManager);
		foreach($FundManager as $key=>$val){
			$data['Manager'][$key] = [
				'Name' => $val['Name'], 
				'AccessionDate' => $val['AccessionDate'], 
				'DimissionDate' => $val['DimissionDate'],
				'args' => rtrim(strtr(base64_encode(serialize(['Name'=>$val['Name'], 'Gender' => $val['Gender']])), '+/', '-_'), '=')
			];
		}
		return $data;
	}
	
	//基金资产行业配置 type 0 资产配置 1 行业配置 2 重仓配置
	public static function getInvestIndustry($InnerCode, $type = 0)
	{
		if($type == 0){//资产配置
			$data = InvestIndustry::getInvestIndustryList($InnerCode);
			if(!$data) return [];
			foreach($data as &$val){
				$val['name'] = $val['IndustryName'];
				$val['RatioInNV'] = sprintf("%.2f", $val['RatioInNV']*100);
				$val['XRatioInNV'] = sprintf("%.2f", $val['XRatioInNV']*100);
			}
		}
		if($type == 1){
			$data = KeyStockPortfolio::getKeyStockPortfolioList($InnerCode);
			if(!$data) return [];
			foreach($data as &$val){
				$val['name'] = $val['SecuAbbr'];
				$val['RatioInNV'] = sprintf("%.2f", $val['RatioInNV']*100);
				$val['XRatioInNV'] = sprintf("%.2f", $val['XRatioInNV']*100);
			}
		}
		if($type == 2){
			$data = BondPortifolioStru::getBondPortifolioStruList($InnerCode);
			if(!$data) return [];
			foreach($data as &$val){
				$val['name'] = $val['BondType'];
				$val['RatioInNV'] = sprintf("%.2f", $val['RatioInNV']*100);
				$val['XRatioInNV'] = sprintf("%.2f", $val['XRatioInNV']*100);
			}
		}
		return array_values($data);
	}
	
	//基金交易须知
	public static function getTradNotice($InnerCode)
	{
		$data = [];
		$FundArchives = FundArchives::find($InnerCode);
		$data['LowestSumPurLL'] = sprintf("%.2f", $FundArchives['LowestSumPurLL']);//最低认购金额下限（元）
		$data['LowestSumRedemption'] = sprintf("%.2f", $FundArchives['LowestSumRedemption']);//最低赎回份额（份）
		$data['LowestSumForHolding'] = sprintf("%.2f", $FundArchives['LowestSumForHolding']);//最低持有份额（份）
		$tg = ChargeRate::getChargeRateFind($InnerCode, 9);//托管费
		$data['TgForHolding'] = sprintf("%.2f", $tg['MinimumChargeRate']*100);
		$gl = ChargeRate::getChargeRateFind($InnerCode, 8);//管理费
		$data['GlForHolding'] = sprintf("%.2f", $gl['MinimumChargeRate']*100);
		$data['SgForHolding'] = ChargeRate::getChargeRateList($InnerCode, 2);//申购费率
		$data['ShForHolding'] = ChargeRate::getChargeRateList($InnerCode, 3);//赎回费率
		return $data;
	}
	
}