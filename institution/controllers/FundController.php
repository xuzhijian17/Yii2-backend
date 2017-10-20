<?php
namespace institution\controllers;

use Yii;
// use common\lib\CommFun;
use institution\service\JavaRestful;

/**
 *基金相关控制器
 */
class FundController extends BaseController
{
	/**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        return true;
    }

	public function actionIndex($value='')
	{
		$postData = Yii::$app->request->post();

		if (Yii::$app->request->isAjax) {
			$postData = Yii::$app->request->post();

			$obj = new JavaRestful('F001', $postData, 0);
	        $productData = $obj->apiRequest();

	        if ($productData && !empty($productData) && isset($productData['code']) && isset($productData['res']) && $productData['code'] == '111') {
	        	Yii::$app->redis->executeCommand('SET',['excel_pro_list',json_encode($productData['res']['listObjects'],JSON_UNESCAPED_UNICODE)]);
	        }else{
	        	$responseData = json_encode($productData);
	        	$params = json_encode($obj->params);
	        	Yii::error("request:url=>{$obj->apiurl},params=>{$params} | response:{$responseData}");
	        }
	        
			return json_encode($productData,JSON_UNESCAPED_UNICODE);
        }else{
        	$obj = new JavaRestful('F002',[],1);
        	$orgdicData = $obj->apiRequest();

        	$advisorCodes = [];
	        if ($orgdicData && !empty($orgdicData) && isset($orgdicData['code']) && isset($orgdicData['res']) && $orgdicData['code'] == '111') {
	        	$advisorCodes = $orgdicData['res'];
	        }else{
	        	$responseData = json_encode($orgdicData);
	        	$params = json_encode($obj->params);
	        	Yii::error("request:url=>{$obj->apiurl},params=>{$params} | response:{$responseData}");
	        }
            
            return $this->render('index',['advisorCodes'=>$advisorCodes?:[]]);
        }
	}

	/*public function actionDataToExcel($value='')
	{
		//引入PHPExcel库文件（路径根据自己情况）
		include '../lib/PHPExcel.php';
		//创建对象
		$excel = new PHPExcel();
		//Excel表格式,这里简略写了8列
		$letter = array('A','B','C','D','E','F','F','G');
		//表头数组
		$tableheader = array('学号','姓名','性别','年龄','班级');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}

		//表格数组
		$data = array(
			array('1','小王','男','20','100'),
			array('2','小李','男','20','101'),
			array('3','小张','女','20','102'),
			array('4','小赵','女','20','103')
		);

		//填充表格信息
		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
				$j++;
			}
		}

		//创建Excel输入对象
		$write = new PHPExcel_Writer_Excel5($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="testdata.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}*/
}