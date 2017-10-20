<?php
namespace backend\controllers;

use Yii;
use yii\db\Query;
use backend\models\Theme;
use backend\models\Category;
use backend\models\CategoryNews;
use backend\models\Hot;
use backend\models\Portfolio;
use backend\models\FundNews;
use backend\models\FundList;
use backend\models\FundInfo;
use backend\models\Cooperation;
use common\lib\CommFun;
use common\lib\UploadHandler;

/**
 * Business controller
 */
class BusinessController extends BaseController
{
    const THEMETYPE = 1;
    const CATEGORYTYPE = 2;
    const HOTTYPE = 3;

    /**
     * index
     */
    public function actionIndex()
    {
        return $this->redirect('category');
        // return $this->render('index');
    }

    /**
     * 财经资讯分类
     */
    public function actionCategoryNews()
    {
        $categoryModel = new CategoryNews();
        $categorys = $categoryModel->catList();

        return $this->render('category-news',['categorys'=>$categorys]);
    }

    /**
     * 添加资讯分类
     */
    public function actionAddNewsCat()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new CategoryNews(['scenario'=>'addNewsCat']);
            if ($model->load($data)) {
                $rs = $model->addNewsCat();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-news-cat');
    }

    /**
     * 编辑资讯分类
     */
    public function actionEditNewsCat()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new CategoryNews(['scenario'=>'editNewsCat']);
            if ($model->load($data)) {
                $rs = $model->editNewsCat();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['id']) && !empty($data['id'])) {
                $instid = Yii::$app->admin->instid;
                $category = (new Query)->select('Category')->from('fund_category_news')->where(['id'=>$data['id'],'Instid'=>$instid,'Status'=>0])->scalar();
            }
        }

        return $this->render('add-news-cat',['category'=>isset($category)?$category:'']);
    }

    /**
     * 删除资讯分类
     */
    public function actionDelNewsCat()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new CategoryNews(['scenario'=>'delNewsCat']);
            if ($model->load($data)) {
                $rs = $model->DelNewsCat();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
     * 财经资讯
     */
    public function actionNews()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundNews();
            if ($model->load($data)) {
                $rs = $model->newsList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('news',['cid'=>isset($data['cid'])?$data['cid']:0]);
    }
    
    /**
     * 添加财经资讯
     */
    public function actionAddNews()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundNews(['scenario'=>'addNews']);
            if ($model->load($data)) {
                $rs = $model->addNews();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-news',['cid'=>isset($data['cid'])?$data['cid']:0]);
    }

    /**
     * 编辑财经资讯
     */
    public function actionEditNews()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundNews(['scenario'=>'editNews']);
            if ($model->load($data)) {
                $rs = $model->editNews();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['id']) && !empty($data['id'])) {
                $instid = Yii::$app->admin->instid;
                $newsData = (new Query)->from('fund_news_'.$instid)->where(['id'=>$data['id'],'Status'=>0])->one();
            }
        }

        return $this->render('add-news',['cid'=>isset($data['cid'])?$data['cid']:0,'newsData'=>isset($newsData)?$newsData:'']);
    }

    /**
     * 删除财经资讯
     */
    public function actionDelNews()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundNews(['scenario'=>'delNews']);
            if ($model->load($data)) {
                $rs = $model->DelNews();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 推荐财经资讯
     */
    public function actionRecommendNews()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundNews(['scenario'=>'recommendNews']);
            if ($model->load($data)) {
                $rs = $model->recommendNews();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 主题列表
     */
    public function actionTheme()
    {
        $data = $this->request();

        $themeModel = new Theme();
        $themes = $themeModel->themeList();
        
        return $this->render('theme',['themes'=>$themes, 'type'=>self::THEMETYPE]);
    }

    /**
     * 添加主题
     */
    public function actionAddTheme()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Theme(['scenario'=>'addTheme']);
            if ($model->load($data)) {
                $rs = $model->addTheme();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-theme');
    }

    /**
     * 编辑主题
     */
    public function actionEditTheme()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new Theme(['scenario'=>'editTheme']);
            if ($model->load($data)) {
                $rs = $model->editTheme();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['id']) && !empty($data['id'])) {
                $instid = Yii::$app->admin->instid;
                $themeData = (new Query)->select('*')->from('fund_theme')->where(['id'=>$data['id'],'Instid'=>$instid,'Status'=>[0,1]])->one();
            }
        }

        return $this->render('add-theme',['themeData'=>isset($themeData)?$themeData:[]]);
    }

    /**
     * 删除主题
     */
    public function actionDelTheme()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Theme(['scenario'=>'delTheme']);
            if ($model->load($data)) {
                $rs = $model->delTheme();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
     * 推荐主题
     */
    public function actionRecommendTheme()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Theme(['scenario'=>'recommendTheme']);
            if ($model->load($data)) {
                $rs = $model->recommendTheme();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 上线主题
     */
    public function actionOnlineTheme()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Theme(['scenario'=>'onlineTheme']);
            if ($model->load($data)) {
                $rs = $model->onlineTheme();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }


    /**
     * 上传主题图片
     */
    public function actionUploadTheme()
    {
        $options = ['upload_dir'=>dirname($_SERVER['SCRIPT_FILENAME']).'/theme/','upload_url'=>\yii\helpers\Url::base().'/theme/'];
        $upload_handler = new UploadHandler($options);
    }

    /**
     * 热销基金
     */
    public function actionHot()
    {
        $data = $this->request();
        
        return $this->render('fund-list',['id'=>0,'type'=>self::HOTTYPE]);
    }


    /**
     * 基金分类列表
     */
    public function actionCategory()
    {
        $categoryModel = new Category();
        $categorys = $categoryModel->catList();

        return $this->render('category',['categorys'=>$categorys, 'type'=>self::CATEGORYTYPE]);
    }

    /**
     * 添加基金分类
     */
    public function actionAddCat()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Category(['scenario'=>'addCat']);
            if ($model->load($data)) {
                $rs = $model->addCat();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-cat');
    }

    /**
     * 编辑基金分类
     */
    public function actionEditCat()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new Category(['scenario'=>'editCat']);
            if ($model->load($data)) {
                $rs = $model->editCat();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['id']) && !empty($data['id'])) {
                $instid = Yii::$app->admin->instid;
                $category = (new Query)->select('Category')->from('fund_category')->where(['id'=>$data['id'],'Instid'=>$instid,'Status'=>0])->scalar();
            }
        }

        return $this->render('add-cat',['category'=>isset($category)?$category:'']);
    }

    /**
     * 删除基金分类
     */
    public function actionDelCat()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Category(['scenario'=>'delCat']);
            if ($model->load($data)) {
                $rs = $model->delCat();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
     * 自定义基金列表
     */
    public function actionFundList()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundList();
            if ($model->load($data)) {
                $rs = $model->fundList();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        $id = isset($data['id']) ? $data['id'] : 0;
        $type = isset($data['type']) ? $data['type'] : '';
        
        return $this->render('fund-list',['id'=>$id,'type'=>$type]);
    }

    /**
     * 添加基金
     */
    public function actionAddFund()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundList(['scenario'=>'addFundList']);
            if ($model->load($data)) {
                $rs = $model->addFund();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
     * 更新基金
     */
    public function actionUpdateFund()
    {
        $id = $this->request('id');
        $tags = $this->request('tags');

        $fundModel = new FundList();
        $rs = $fundModel->update($id, $tags);

        return $this->renderJson($rs);
    }

    /**
     * 删除基金
     */
    public function actionDelFund()
    {
        $id = $this->request('id');

        $fundModel = new FundList();
        $rs = $fundModel->delete($id);

        return $this->renderJson($rs);
    }

    /**
     * 置顶基金
     */
    public function actionTopFund()
    {
        $id = $this->request('id');
        $istop = $this->request('istop');

        $fundModel = new FundList();
        $rs = $fundModel->istop($id,$istop);

        return $this->renderJson($rs);
    }

    /**
     * 推荐基金
     */
    public function actionRecommend()
    {
        $id = $this->request('id');
        $recommend = $this->request('recommend');

        $fundModel = new FundList();
        $rs = $fundModel->recommend($id,$recommend);

        return $this->renderJson($rs);
    }

    /**
     * 添加基金时自动补全可购买基金列表
     */
    public function actionHsFundList($value='')
    {
        if (!Yii::$app->request->isAjax) {
            return CommFun::renderFormat('102');
        }

        $model = new FundList();
        $rs = $model->getFundInfo();
        
        return $this->renderJson($rs);
    }
    
    /**
     * 组合列表
     */
    public function actionPortfolio($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio();
            if ($model->load($data)) {
                $rs = $model->portfolioList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('portfolio');
    }
    
    /**
     * 添加组合
     */
    public function actionAddPortfolio($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio(['scenario'=>'addPortfolio']);
            if ($model->load($data)) {
                $rs = $model->addPortfolio();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-portfolio');
    }

    /**
     * 编辑组合
     */
    public function actionEditPortfolio($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio(['scenario'=>'editPortfolio']);
            if ($model->load($data)) {
                $rs = $model->editPortfolio();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['PortfolioId']) && !empty($data['PortfolioId'])) {
                $portfolioData = (new Query)->from('portfolio_config')->where(['PortfolioId'=>$data['PortfolioId']])->one();
                !empty($portfolioData) && $portfolioData['ExpectInfo'] = json_decode($portfolioData['ExpectInfo'],true);
            }
        }

        return $this->render('add-portfolio',['portfolioData'=>isset($portfolioData)?$portfolioData:[]]);
    }

    /**
     * 上线组合
     */
    public function actionOnlinePortfolio()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio(['scenario'=>'onlinePortfolio']);
            if ($model->load($data)) {
                $rs = $model->onlinePortfolio();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 删除组合
     */
    public function actionDelPortfolio()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio(['scenario'=>'delPortfolio']);
            if ($model->load($data)) {
                $rs = $model->delPortfolio();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 组合基金列表
     */
    public function actionPortfolioFundList($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Portfolio(['scenario'=>'portfolioFundList']);
            if ($model->load($data)) {
                $rs = $model->portfolioFundList();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        $portfolioId = isset($data['PortfolioId']) ? $data['PortfolioId'] : '';
        
        return $this->render('portfolio-fund-list',['portfolioId'=>$portfolioId]);
    }

    /**
     * 添加组合基金
     */
    public function actionAddPortfolioFund($value='')
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new Portfolio();
            $rs = $model->addPortfolioFund($data);
            
            return $this->renderJson($rs);
        }
    }

    /**
     * 获取组合单只基金最低起购金额
     */
    public function actionGetLowestSumll($value='')
    {
        $fundCode = $this->request('fundCode');

        $rs = \frontend\modules\api\services\QueryServiceApi::GetLimitInfo($fundCode);
        
        return $this->renderJson($rs);
    }

    /**
     * 可购买基金列表（基金维护）
     */
    public function actionFundInfo($value='')
    {
        $data = $this->request();

        $model = new FundInfo();
        if (Yii::$app->request->isAjax) {
            if ($model->load($data)) {
                $rs = $model->fundInfo($data);
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        // 统计数据
        $StatisticalData = $model->getStatisticalData();

        return $this->render('fund-info',['StatisticalData'=>$StatisticalData]);
    }

    /**
     * 编辑基金信息（基金维护）
     */
    public function actionEditFundInfo($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new FundInfo(['scenario'=>'update']);
            if ($model->load($data)) {
                $rs = $model->update();
            } else {
                $rs = $model->errorss;
            }

            return $this->renderJson($rs);
        }else{
            if (isset($data['fundCode']) && !empty($data['fundCode'])) {
                $data = (new Query)->from('fund_info')->where(['FundCode'=>$data['fundCode']])->one();
            }
        }
        
        return $this->render('edit-fund-info',['data'=>isset($data)?$data:[]]);
    }

    /**
     * Business cooperation
     *
     * @return html|json
     */
    public function actionCooperation()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new Cooperation();
            if ($model->load($data)) {
                $rs = $model->applyList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('cooperation');
    }

    /**
     * Business account
     *
     * @return html|json
     */
    public function actionBaccount()
    {
        $data = $this->request();
        
        if (!Yii::$app->request->isAjax) {
            $model = new \backend\models\User();
            if ($model->load($data)) {
                $rs = $model->baccountList();
            } else {
                $rs = $model->errors;
            }
            
            // return $this->renderJson($rs);
        }
        
        return $this->render('baccount-list',['baccountData'=>$rs]);
    }

    /**
     * Business account
     *
     * @return html|json
     */
    public function actionAddBaccount()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new \backend\models\User(['scenario'=>'addBaccount']);
            if ($model->load($data)) {
                $rs = $model->addBaccount();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        return $this->render('add-baccount');
    }

    /**
     * Business account
     *
     * @return html|json
     */
    public function actionEditBaccount()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new \backend\models\User(['scenario'=>'editBaccount']);
            if ($model->load($data)) {
                $rs = $model->editBaccount();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }else{
            if (isset($data['uid']) && !empty($data['uid'])) {
                $sql = "SELECT * FROM `user` u LEFT JOIN user_bank ub ON u.id=ub.Uid LEFT JOIN company_attach ca ON u.id=ca.Uid WHERE u.OpenStatus=1 AND u.Instid=1000 AND u.id=:uid";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindParam(':uid', $data['uid']);
                $baccountData = $command->queryOne();
                if ($baccountData && !empty($baccountData)) {
                    $baccountData['TradePass'] = CommFun::AutoEncrypt($baccountData['Pass'],'D');
                }
            }
        }

        return $this->render('add-baccount',['baccountData'=>isset($baccountData)?$baccountData:'']);
    }

    /**
     * Trade remit
     *
     * @return html|json
     */
    public function actionTradeRemit()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new \backend\models\Trade(['scenario'=>'btradeRemit']);
            if ($model->load($data)) {
                $rs = $model->bTradeList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
        
        return $this->render('btrade-list');
    }

    /**
     * Detail remit
     *
     * @return html|json
     */
    public function actionDetailRemit()
    {
        $data = $this->request();

        if (isset($data['id']) && !empty($data['id'])) {
            $sql = "SELECT * FROM trade_order_1000 tod LEFT JOIN trade_order_1000_attach toa ON tod.id=toa.TradeOrderId LEFT JOIN company_attach ca ON ca.Uid=tod.Uid LEFT JOIN fund_info fi ON tod.FundCode=fi.FundCode WHERE id=:id";
            $command = Yii::$app->db->createCommand($sql);
            $command->bindParam(':id', $data['id']);
            $rs = $command->queryOne();
            if ($rs && !empty($rs)) {
                // Format trade type
                if ($rs['TradeType'] === '0') {
                    $rs['TradeTypeName'] = '申购';
                }elseif ($rs['TradeType'] === '1') {
                    $rs['TradeTypeName'] = '赎回';
                }elseif ($rs['TradeType'] === '2') {
                    $rs['TradeTypeName'] = '撤单';
                }elseif ($rs['TradeType'] === '3') {
                    $rs['TradeTypeName'] = '定投';
                }else{
                    $rs['TradeTypeName'] = '';
                }

                // Format trade status
                if ($rs['TradeStatus'] == '9' && $rs['TradeType'] == '0') {
                    $rs['TradeStatusName'] = '申购中';
                }elseif ($rs['TradeStatus'] == '9' && $rs['TradeType'] == '1') {
                    $rs['TradeStatusName'] = '赎回中';
                }elseif ($rs['TradeStatus'] === '4') {
                    $rs['TradeStatusName'] = '撤单';
                }elseif (in_array($rs['TradeStatus'], ['1','2','3'])) {
                    $rs['TradeStatusName'] = '成功';
                }else{
                    $rs['TradeStatusName'] = '失败';
                }

                // Format share type
                if ($rs['ShareType'] === 'A') {
                    $rs['ShareTypeName'] = '前端收费';
                }elseif ($rs['ShareType'] === 'B') {
                    $rs['ShareTypeName'] = '后端收费';
                }elseif ($rs['ShareType'] === 'B') {
                    $rs['ShareTypeName'] = '其它';
                }else{
                    $rs['ShareTypeName'] = '';
                }
                
            }
        }
        
        return $this->render('btrade-detail',['rs'=>isset($rs)?$rs:[]]);
        
    }

    /**
     * Upload remit
     *
     * @return html|json
     */
    public function actionUploadRemit()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new \backend\models\Trade(['scenario'=>'uploadRemit']);
            if ($model->load($data)) {
                $rs = $model->uploadRemit();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }else{
            if (isset($data['id']) && !empty($data['id'])) {
                $sql = "SELECT * FROM `trade_order_1000_attach` WHERE TradeOrderId=:id";
                $command = Yii::$app->db->createCommand($sql);
                $command->bindParam(':id', $data['id']);
                $remitImageData = $command->queryOne();
            }
        }

        return $this->render('upload-remit',['remitImageData'=>isset($remitImageData)?$remitImageData:[]]);
        
        
    }

    /**
     * Upload remit image
     *
     * @return html|json
     */
    public function actionUploadRemitImage()
    {
        $data = $this->request();
        
        // var_dump(Yii::getAlias('@web').'/remit',\yii\helpers\Url::base().'/remit',$_SERVER);exit();
        $options = ['upload_dir'=>dirname($_SERVER['SCRIPT_FILENAME']).'/remit/','upload_url'=>\yii\helpers\Url::base().'/remit/'];
        $upload_handler = new UploadHandler($options);
    }

    /**
     * Ensure remit
     *
     * @return html|json
     */
    public function actionEnsureRemit()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new \backend\models\Trade(['scenario'=>'ensureRemit']);
            if ($model->load($data)) {
                $rs = $model->ensureRemit();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * Systen operation
     *
     * @return html
     */
    public function actionSystemOperation()
    {
        $data = $this->request();

        /*if (Yii::$app->request->isAjax) {
            exec("ls",$outPut,$rCode);
            if ($rCode === 0) {
                // $outPut = system("ls");
                $rs = ['outPut'=>$outPut];
            } else {
                $rs = CommFun::renderFormat('111',['returnCode'=>$rCode],$outPut);
            }
            
            return $this->renderJson($rs);
        }*/
        
        return $this->render('system-operation');
    }

    /**
     * Open Quotation
     *
     * @return html
     */
    public function actionOpenQuotation($value='')
    {
        $cmd = Yii::$app->params['cronUrl']['cron_fundinfo'];

        exec($cmd,$outPut,$rCode);
        echo "<pre>";
        foreach ($outPut as $key => $value) {
            print_r($value."<br>");
        }
        echo "</pre>";

        if ($rCode === 0) {
            echo "脚本执行完成!";
        }else{
            echo "脚本执行失败! code:".$rCode;
        }
    }

    /**
     * Close Quotation
     *
     * @return html
     */
    public function actionCloseQuotation($value='')
    {
        $cmd = Yii::$app->params['cronUrl']['cron_confirmback'];
        
        exec($cmd,$outPut,$rCode);
        echo "<pre>";
        foreach ($outPut as $key => $value) {
            print_r($value."<br>");
        }
        echo "</pre>";

        if ($rCode === 0) {
            echo "脚本执行完成!";
        }else{
            echo "脚本执行失败! code:".$rCode;
        }
    }

    /**
     * Suspend subscribe
     *
     * @return html
     */
    public function actionSuspendSubscribe($value='')
    {
        $cmd = Yii::$app->params['cronUrl']['suspend'];
        
        exec($cmd,$outPut,$rCode);
        echo "<pre>";
        foreach ($outPut as $key => $value) {
            print_r($value."<br>");
        }
        echo "</pre>";

        if ($rCode === 0) {
            echo "脚本执行完成!";
        }else{
            echo "脚本执行失败! code:".$rCode;
        }
    }

    /**
     * Recover subscribe
     *
     * @return html
     */
    public function actionRecoverSubscribe($value='')
    {
        $cmd = Yii::$app->params['cronUrl']['recover'];
        
        exec($cmd,$outPut,$rCode);
        echo "<pre>";
        foreach ($outPut as $key => $value) {
            print_r($value."<br>");
        }
        echo "</pre>";

        if ($rCode === 0) {
            echo "脚本执行完成!";
        }else{
            echo "脚本执行失败! code:".$rCode;
        }
    }
}
