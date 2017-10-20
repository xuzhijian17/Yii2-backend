<?php
namespace frontend\controllers;

use Yii;
use common\lib\CommFun;
use frontend\models\FundInfo;
use yii\helpers\Url;

class WebsiteController extends Controller
{
    public function init()
    {
        
    }
	
    public function actionIndex()
    {
        echo 'hello, welcome to 汇成基金';
        exit();
//         $fundtype = empty($this->post('fundtype'))?'':$this->post('fundtype');
// 		$fundcompany = empty($this->post('fundcompany'))?'':$this->post('fundcompany');
		
// 		$fundcode = empty($this->post('fundcode'))?'':$this->post('fundcode');
// 		$orderby = empty($this->post('orderby'))?'RRInSelectedMonth':$this->post('orderby');
		
// 		$page = empty($this->post('page'))?'1':$this->post('page');
// 		$pagesize = empty($this->post('pagesize'))?'10':$this->post('pagesize');
		
// 		$callback = $this->get('callback');
		
// 		$stwhere = "1";
		
// 		if(!empty($fundtype))  $stwhere.=" and FundType='".$fundtype."'";
// 		if(!empty($fundcompany)) $stwhere.=" and FundCompany='".$fundcompany."'";
// 		if(!empty($fundcode))   $stwhere.=" and FundCode='".$FundCode."'";
		
// 		$data = FundInfo::getFundInfo($page,$pagesize,$orderby,$stwhere);
		
// 		$count = FundInfo::getFundCount($stwhere);
		
// 		if(empty($data)){
// 			 $return['code'] = '-101';
// 			 $return['msg'] = '当前条件没有数据';
// 			 $htmlstr = "<ul class='nodate'><li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li>";
// 			 $htmlstr .= "<li></li></ul>";
			 
// 			 $return['htmlstr'] = $htmlstr;
			
// 		}else{
// 			$return['code'] = '200';
// 			$return['msg'] = '成功';
// 			$return['total'] = $count;
// 			$return['page'] = $page;
			
// 			$htmlstr = '';
// 			foreach($data as $key => $value)
// 			{
// 			     $htmlstr .= "<ul class='content'><li>".$value['FundCode']."</li>";
//                  $htmlstr .= "<li>".$value['FundName']."</li>";
//                  $htmlstr .= "<li>".$value['PernetValue']."</li>";
//                  $htmlstr .= "<li>".$value['DailyProfit']."</li>";
//                  $htmlstr .= "<li>".$value['RRInSelectedMonth']."</li>";
// 				 $htmlstr .= "<li>".$value['RRInThreeMonth']."</li>";
// 				 $htmlstr .= "<li>".$value['RRInSixMonth']."</li>";
// 				 $htmlstr .= "<li>".$value['RRInSingleYear']."</li>";
// 				 $htmlstr .= '<li><a href="javascript:void(0);" class="opBut">购买</a></li></ul>';
// 			}
			
// 			$return['html'] = $htmlstr;
// 		}
		
// 		echo $callback."(".json_encode($return).")";
// 		exit;
    }    
}