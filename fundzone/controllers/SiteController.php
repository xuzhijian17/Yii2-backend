<?php
namespace fundzone\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use fundzone\models\Product;
use fundzone\models\News;
use fundzone\models\Fund;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function init()
    {
        $systemconfig = Yii::$app->db->createCommand("select *from system_config where id=1")->queryOne();

        $this->view->params['title'] = isset($systemconfig['Title']) ? $systemconfig['Title'] : '';
        $this->view->params['keywords'] = isset($systemconfig['Keywords']) ? $systemconfig['Keywords'] : '';
        $this->view->params['descriptions'] = isset($systemconfig['Descriptions']) ? $systemconfig['Descriptions'] : '';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    // 首页
    public function actionIndex()
    {
        $model = new Product();
        $recommend = $model->getRecommend();
        $fundType = $model->getFundType();
        $stockFundList = $model->getFundList(['fundtype'=>'股票型','pageSize'=>10]);
        $mixFundList = $model->getFundList(['fundtype'=>'混合型','pageSize'=>10]);
        $bondFundList = $model->getFundList(['fundtype'=>'债券型','pageSize'=>10]);
        $sbondFundList = $model->getFundList(['fundtype'=>'短期理财债券型','pageSize'=>10]);
        $moneyFundList = $model->getFundList(['fundtype'=>'货币型','pageSize'=>10]);
        $qdiiFundList = $model->getFundList(['fundtype'=>'QDII','pageSize'=>10]);

        return $this->render('index',['recommend'=>$recommend?:[],'fundType'=>$fundType?:[],'stockFundList'=>$stockFundList?:[],'mixFundList'=>$mixFundList?:[],'bondFundList'=>$bondFundList?:[],'sbondFundList'=>$sbondFundList?:[],'moneyFundList'=>$moneyFundList?:[],'qdiiFundList'=>$qdiiFundList?:[]]);
    }

    // 热销基金
    public function actionRecommend()
    {
        $data = Yii::$app->request->get();
        
        $model = new Product();
        $rs = $model->getRecommend($data);
        
        return json_encode($rs,JSON_UNESCAPED_UNICODE);
    }

    // 首页基金推荐产品列表
    public function actionGetFundList()
    {
        $data = Yii::$app->request->get();
        
        $model = new Product();
        $fundList = $model->getFundList($data);
        
        return json_encode($fundList,JSON_UNESCAPED_UNICODE);
    }
	
    // 基金产品
	public function actionProduct()
    {
		$model = new Product();
		$fundType = $model->getFundType();
		$fundCompany = $model->getFundCompany();
		
        return $this->render('product',['fundType'=>$fundType?:[],'fundCompany'=>$fundCompany?:[]]);
    }
    
    // 基金产品列表
    public function actionFundList()
    {
        $data = Yii::$app->request->get();
        
        $model = new Product();
        $fundList = $model->fundList($data);
        
        return json_encode($fundList,JSON_UNESCAPED_UNICODE);
    }

    //基金详情
    public function actionDetail()
    {
        $fundCode = Yii::$app->request->get('fundcode','');
        $type = Yii::$app->request->get('type','');

        $fundModel = new Fund();
        $detailData = $fundModel->getFundDetail($fundCode);

        $netValueData = [];
        $netValueData['oneMonth'] = $fundModel->getNetValue($fundCode,date("Y-m-d",time()-3600*24*30));
        $netValueData['thrMonth'] = $fundModel->getNetValue($fundCode,date("Y-m-d",time()-3600*24*30*3));
        $netValueData['sixMonth'] = $fundModel->getNetValue($fundCode,date("Y-m-d",time()-3600*24*30*6));
        $netValueData['tweMonth'] = $fundModel->getNetValue($fundCode,date("Y-m-d",time()-3600*24*30*12));
        $netValueData['allMonth'] = $fundModel->getNetValue($fundCode);

        $managerData = $fundModel->fundManager($fundCode);
        $profitGuide = $fundModel->profitGuide($fundCode);
        $fundArchivesData = $fundModel->fundArchives($fundCode);
        $positionInfoData = $fundModel->positionInfo($fundCode);
        $fundBulletinData = $fundModel->fundBulletin($fundCode);
        $participationProfit = $fundModel->participationProfit($fundCode);

        // var_dump($detailData);exit();
        return $this->render('detail',['detailData'=>$detailData?:[],'netValueData'=>$netValueData,'managerData'=>$managerData?:[],'profitGuide'=>$profitGuide?:[],'fundArchivesData'=>$fundArchivesData?:[],'positionInfoData'=>$positionInfoData?:[],'fundBulletinData'=>$fundBulletinData?:[],'participationProfit'=>$participationProfit?:[]]);
    }

    //收益走势图
    public function actionProfitPerformance($value='')
    {
        $fundCode = Yii::$app->request->get('fundcode','770001');
        $startDay = Yii::$app->request->get('startDay','2016-07-01');

        $fundModel = new Fund();
        $detailData = $fundModel->getNetValue($fundCode,$startDay);

        var_dump($detailData);
    }

    //基金经理人头像
    public function actionManagerAvatar()
    {
        // ob_end_clean();
        header("Content-type: image/gif");

        $ID = Yii::$app->request->get('ID','');

        $managerData = (new \yii\db\Query())
            ->from('MF_FundManager')
            ->where(['ID' => $ID])
            ->one(Yii::$app->db_juyuan)
        ;
        echo $managerData["PersonalData"]?:'/images/photo.jpg';
    }

    //基金公告详情
    public function actionFundBulletinDetail()
    {
        $ID = Yii::$app->request->get('ID');

        $fundModel = new Fund();
        $bulletinDetailData = $fundModel->bulletinDetail($ID,1);

        return $this->render('bulletin-detail',['bulletinDetailData'=>$bulletinDetailData?:[]]);
    }
	
    //信息咨询
	public function actionNews()
    {
        $cid = Yii::$app->request->get('cid',1);    // 测试cid=27
        $page = Yii::$app->request->get('page',1);
        $pageSize = Yii::$app->request->get('pageSize',15);
        
        $model = new News();
        $Categorys = $model->getCatNews();

        $NewsList = $model->getNewsList($cid, $page, $pageSize);
        
        return $this->render('news',['cid'=>$cid,'Categorys'=>$Categorys?:[],'NewsList'=>$NewsList?:[]]);
    }

    //信息咨询详情
    public function actionNewsDetail()
    {
        $id = Yii::$app->request->get('id');

        $model = new News();
        $Categorys = $model->getCatNews();

        $NewsDetail = $model->getNewsDetail($id);
		
		$this->view->params['title'] = isset($NewsDetail['Title']) ? $NewsDetail['Title'] : '';
        $this->view->params['keywords'] = isset($NewsDetail['Keywords']) ? $NewsDetail['Keywords'] : '';
        $this->view->params['descriptions'] = isset($NewsDetail['Descriptions']) ? $NewsDetail['Descriptions'] : '';
        return $this->render('news-detail',['id'=>$id,'Categorys'=>$Categorys?:[],'NewsDetail'=>$NewsDetail?:[]]);
    }

    /*public function actionNews()
    {
        return $this->render('news');
    }

    public function actionNewsDetail()
    {
        $data = Yii::$app->request->get();

		return $this->render('news-detail'.$data['id']);
    }*/
	
	public function actionDynamic()
    {
        return $this->render('dynamic');
    }

    public function actionDynamicDetail()
    {
        $data = Yii::$app->request->get();

		return $this->render('dynamic-detail'.$data['id']);
    }
	
	public function actionBulletin()
    {
        return $this->render('bulletin');
    }

    public function actionBulletinDetail()
    {
        $data = Yii::$app->request->get();

		return $this->render('bulletin-detail'.$data['id']);
    }
	
	public function actionInfomation()
    {
        return $this->render('infomation');
    }

    public function actionInfomationDetail()
    {
        $data = Yii::$app->request->get();

		return $this->render('infomation-detail'.$data['id']);
    }
	
	public function actionCallCenter()
    {
        return $this->render('call-center');
    }

    public function actionTradeGuide()
    {
        return $this->render('jiaoyiGuide');
    }

    public function actionTradeQuota()
    {
        return $this->render('jiaoyiQuota');
    }
	
    public function actionBasis()
    {
        return $this->render('basis');
    }

    public function actionInvestor()
    {
        return $this->render('investor');
    }

    public function actionRiskWarning()
    {
        return $this->render('risk-warning');
    }

    public function actionAptitude()
    {
        return $this->render('aptitude');
    }
	
    public function actionPractitioners()
    {
        return $this->render('practitioners');
    }

    public function actionAntiMoneyLaundering()
    {
        return $this->render('anti-money-laundering');
    }

    public function actionFeedback()
    {
        return $this->render('feedback');
    }

	public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionCulture()
    {
        return $this->render('culture');
    }

    public function actionContact()
    {
        return $this->render('contact');
    }

    public function actionRecruit()
    {
        return $this->render('recruit');
    }
	
    public function actionOpenAccount()
    {
        return $this->render('open-account');
    }
	
    public function actionClosAccount()
    {
        return $this->render('clos-account');
    }
	
    public function actionDataChange()
    {
        return $this->render('data-change');
    }
	
    public function actionOnlineTrading()
    {
        return $this->render('online-trading');
    }
	
    public function actionDownload()
    {
        return $this->render('download');
    }
}
