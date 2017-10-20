<?php
namespace frontend\models;
use Yii;
class KeyStockPortfolio extends Model
{
	public static $tableName = 'MF_KeyStockPortfolio';
	public static function getKeyStockPortfolioList($InnerCode){
		$dateType = ['-03-31', '-06-30', '-09-30', '-12-31'];
		$param = [
			'field' => 'max(ReportDate) ReportDate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'ReportDate desc, RatioInNV desc',
		];
		$data = KeyStockPortfolio::_find($param);
		$maxDate = $data['ReportDate'];
		$key = array_search(substr($data['ReportDate'], 4, 6),$dateType);
		$minDate = $key == 0 ? (substr($data['ReportDate'], 0, 4) - 1).$dateType[3] :
						substr($data['ReportDate'], 0, 4).$dateType[$key-1];
		$param = [
			'field' => 'MF_KeyStockPortfolio.*, SecuMain.SecuAbbr',
			'join' => 'left join SecuMain on SecuMain.InnerCode = MF_KeyStockPortfolio.StockInnerCode',
			'where' => 'MF_KeyStockPortfolio.InnerCode = '.$InnerCode.' and MF_KeyStockPortfolio.ReportDate >= "'.$minDate.'"',
		];
		$data = KeyStockPortfolio::_query($param);
		if(!$data) return []; $list = $plist = $nlist = []; 
		foreach($data as $val){
			if($val['ReportDate'] == $maxDate){
				$plist[$val['StockInnerCode']] = $val;
			}else{
				$nlist[$val['StockInnerCode']] = $val;
			}
		}
		foreach($plist as $key=>$val){
			$list[$key] = $val;
			if(!isset($nlist[$key])){
				$list[$key]['XRatioInNV'] = $val['RatioInNV']; continue;
			}
			$list[$key]['XRatioInNV'] = $val['RatioInNV'] - $nlist[$key]['RatioInNV'];
		}
		return $list;
	}
}