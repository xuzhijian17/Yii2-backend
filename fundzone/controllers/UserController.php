<?php
namespace fundzone\controllers;

use fundzone\models\CompanyUser;
use Yii;

/**
 * 个人中心
 * Class UserController
 * @package fundzone\controllers
 */
class UserController extends BaseController
{
    public $uid;//uid属性

    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->uid = isset($this->user['id'])?$this->user['id']:0;
    }

    //交易记录列表
    public function actionLoginout()
    {
        $session = Yii::$app->session;
        unset($session['user_login'],$session['company_attach'], $session['first_login']);
        Yii::$app->user->logout();
        return $this->redirect("/");
    }

    //修改登陆密码
    public function actionChangepassword()
    {
        return $this->render('changepassword');
    }

    public function actionChangepasswordverify()
    {
        $return = ['code'=>-1, 'message'=>'请求有误!'];
        if (!$this->isAjax()) {
            exit(json_encode($return));
        }
        $old_password = $this->post('old_password');
        $new_password = $this->post('new_password');
        $new_password2 = $this->post('new_password2');

        if (empty($old_password) || empty($new_password) || empty($new_password2)) {
            $return['message'] = '必填项不能为空!';
            exit(json_encode($return));
        }

        $companyUser = new CompanyUser();
        $user_info = $companyUser->getUserInfoByUid($this->uid);
        if (empty($user_info)) {
            $return['message'] = '该账户不存在!';
            exit(json_encode($return));
        }

        $old_password = md5($old_password);
        if ( $old_password != $user_info['LoginPass']) {
            $return['message'] = '该账户原密码不正确!';
            exit(json_encode($return));
        }
        if ($new_password != $new_password2) {
            $return['message'] = '新密码两次输入不一致!';
            exit(json_encode($return));
        }
        $return['code'] = 0;
        $return['message'] = '修改成功!';
        $companyUser->updateAll(['LoginPass'=>md5($new_password)], ['id'=>$this->uid]);
        exit(json_encode($return));
    }
}
