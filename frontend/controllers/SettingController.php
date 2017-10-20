<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\User;
use yii\helpers\Json;
use frontend\models\FundAccount;
use frontend\models\Password;
use common\lib\CommFun;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class SettingController extends Controller
{
    public $layout = 'setting';

    /**
     * 配置行为列表，组件会自动附加该行为，并且会调用行为对象中的attach()方法
     * Returns a list of behaviors that this component should behave as.
     *
     * Child classes may override this method to specify the behaviors they want to behave as.
     *
     * The return value of this method should be an array of behavior objects or configurations
     * indexed by behavior names. A behavior configuration can be either a string specifying
     * the behavior class or an array of the following structure:
     *
     * ```php
     * 'behaviorName' => [
     *     'class' => 'BehaviorClass',
     *     'property1' => 'value1',
     *     'property2' => 'value2',
     * ]
     * ```
     *
     * Note that a behavior class must extend from [[Behavior]]. Behavior names can be strings
     * or integers. If the former, they uniquely identify the behaviors. If the latter, the corresponding
     * behaviors are anonymous and their properties and methods will NOT be made available via the component
     * (however, the behaviors can still respond to the component's events).
     *
     * Behaviors declared in this method will be attached to the component automatically (on demand).
     *
     * @return array the behavior configurations.
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
        // parent::init();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->title = '设置';

        $uid = Yii::$app->user->id;

        $userInfo = FundAccount::getAccountInfo($uid);
        
        return $this->render('index',['rs'=>CommFun::renderFormat(0, $userInfo)]);
    }

    public function actionSetPassword($value='')
    {
        $this->view->title = '修改密码';

        return $this->render('setPassword');
    }

    public function actionModifyPassword($value='')
    {
        $uid = Yii::$app->user->id;

        $data = Yii::$app->request->post();

        // 若不传场景参数，默认使用default场景。所有的rules验证规则，若不指定场景名或指定场景为'on'=>'default'，则都会默认使用该场景
        $userModel = new Password(['scenario'=>'modifyPassword']);
        // 使用attributes设置属性，默认需要对所有传入的属性进行对应的场景规则(rules)验证，对没有进行规则验证的属性，将获取不到该属性的值。或者设置setAttributes()方法中的第2个参数为false来取消规则验证
        $userModel->setAttributes($data, true);     // $userModel->attributes = $data;
        $userModel->uid = $uid;     // 直接设置，无需进行场景规则验证
        
        if ($userModel->validate()) {
            // 所有输入数据都有效 all inputs are valid
            $rs = $userModel->setPassword($uid);
        } else {
            // 验证失败：$errors 是一个包含错误信息的数组
            $errors = $userModel->errors;
            $rs = CommFun::renderFormat('-100',$errors);
        }

        return Json::encode($rs);
    }

    public function actionHelpCenter($value='')
    {
        $this->view->title = '帮助中心';

        return $this->render('helpCenter');
    }

    public function actionFeedback($value='')
    {
        $this->view->title = '意见反馈';

        return $this->render('feedback');
    }

    public function actionAboutUs($value='')
    {
        $this->view->title = '关于我们';

        return $this->render('aboutUs');
    }

    public function actionLogout($value='')
    {
        Yii::$app->session->destroy();

        return $this->redirect(Yii::$app->user->loginUrl);
    }
}
