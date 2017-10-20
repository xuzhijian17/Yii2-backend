<?php
namespace frontend\models;
use Yii;
class InvestIndustry extends Model
{
	public static $tableName = 'MF_InvestIndustry';
	public static function getInvestIndustryList($InnerCode){
		$dateType = ['-03-31', '-06-30', '-09-30', '-12-31'];
		$param = [
			'field' => 'max(ReportDate) ReportDate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'ReportDate desc, RatioInNV desc',
		];
		$data = InvestIndustry::_find($param);
		$maxDate = $data['ReportDate'];
		$key = array_search(substr($data['ReportDate'], 4, 6),$dateType);
		$minDate = $key == 0 ? (substr($data['ReportDate'], 0, 4) - 1).$dateType[3] :
						substr($data['ReportDate'], 0, 4).$dateType[$key-1];
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and ReportDate >= "'.$minDate.'"',
		];
		$data = InvestIndustry::_query($param);
		if(!$data) return []; $list = $plist = $nlist = []; 
		foreach($data as $val){
			if($val['ReportDate'] == $maxDate){
				$plist[$val['IndustryCode']] = $val;
			}else{
				$nlist[$val['IndustryCode']] = $val;
			}
		}
		//return $data;
		foreach($plist as $key=>$val){
			foreach($plist as $key=>$val){
				$list[$key] = $val;
				if(!isset($nlist[$key])){
					$list[$key]['XRatioInNV'] = $val['RatioInNV']; continue;
				}
				$list[$key]['XRatioInNV'] = $val['RatioInNV'] - $nlist[$key]['RatioInNV'];
			}
		}
		return $list;
	}
}