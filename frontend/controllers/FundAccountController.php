<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\User;
use yii\helpers\Json;
use frontend\models\FundAccount;
use common\lib\CommFun;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class FundAccountController extends Controller
{
    // public $layout = 'fund-account';

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

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        if (!Yii::$app->session['user_login']) {
            // throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            $this->redirect(Yii::$app->user->loginUrl);
            return false;
        }

        return true;
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {

    }

    /**
     * Displays fund account.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->title = '基金账户';

        $uid = Yii::$app->session['user_login']['id'];  //Yii::$app->user->id;
        
        $fundAccountData =FundAccount::accountAssets($uid);   // 持有中基金数据
        $fundAccountData['tradelist'] = FundAccount::tradelist($uid);   // 处理中基金数据
        
        return $this->renderPartial('index',['rs'=>CommFun::renderFormat(0,$fundAccountData)]);
    }

    /*public function actionRemoveProcess($value='')
    {
        $uid = Yii::$app->session['user_login']['id'];   //Yii::$app->request->get('uid',0);
        $status = Yii::$app->request->get('status',0);

        $data = json_encode(['uid'=>$uid,'status'=>$status]);
        $rs = Yii::$app->redis->executeCommand('HSET',['userTrade', 'tradeStatus', $data]);

        return Json::encode($rs);
    }*/
}
