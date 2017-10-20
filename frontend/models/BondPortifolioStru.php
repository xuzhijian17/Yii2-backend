<?php
namespace frontend\models;
use Yii;
//基金债券组合结构信息
class BondPortifolioStru extends Model
{
	public static $tableName = 'MF_BondPortifolioStru';
	public static function getBondPortifolioStruList($InnerCode){
		$dateType = ['-03-31', '-06-30', '-09-30', '-12-31'];
		$param = [
			'field' => 'max(ReportDate) ReportDate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'ReportDate desc, RatioInNV desc',
		];
		$data = BondPortifolioStru::_find($param);
		$maxDate = $data['ReportDate'];
		$key = array_search(substr($data['ReportDate'], 4, 6),$dateType);
		$minDate = $key == 0 ? (substr($data['ReportDate'], 0, 4) - 1).$dateType[3] :
						substr($data['ReportDate'], 0, 4).$dateType[$key-1];
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and ReportDate >= "'.$minDate.'"',
		];
		$data = BondPortifolioStru::_query($param);
		if(!$data) return []; $list = $plist = $nlist = []; 
		foreach($data as $val){
			if($val['ReportDate'] == $maxDate){
				$plist[$val['BondType']] = $val;
			}else{
				$nlist[$val['BondType']] = $val;
			}
		}
		foreach($plist as $key=>$val){
			$list[$key] = $val;
			if(!isset($nlist[$key])){
				$list[$key]['XRatioInNV'] = $val['RatioInNV'];
			}
			$list[$key]['XRatioInNV'] = $val['RatioInNV'] - $nlist[$key]['RatioInNV'];
		}
		return $list;
	}
}