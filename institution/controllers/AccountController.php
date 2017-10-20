<?php
namespace institution\controllers;

use institution\models\ImageFile;
use institution\service\GridFsService;
use Yii;
use institution\service\AccountService;
use yii\console\Exception;
use yii\mongodb\file\Query;


/**
 *用户账户相关控制器
 */
class AccountController extends BaseController
{
    public $uid;//uid属性
    
    public function init()
    {
        parent::init();
    }
    /**
     * 登录页面
     */
    public function actionLogin()
    {
        return $this->render('login');
    }
    /**
     * 提交登录
     */
    public function actionDologin()
    {
        $post = $this->post();
        if (empty($post['account']) || empty($post['password']))
        {
            echo json_encode(['code'=>'-101','desc'=>'账号/密码不能为空']);
            return false;
        }
        $obj = new AccountService();
        $res = $obj->DoLogin($post['account'], $post['password']);
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 注销登录
     */
    public function actionLoginout()
    {
        $obj = new AccountService();
        $obj->LoginOut();
        return $this->runAction('login');
    }

    /**
     * 修改密码
     */
    public function actionUpdatePwd()
    {
        $user_login = Yii::$app->session['user_login'];

        if (Yii::$app->request->isAjax) {
            $post = $this->post();

            $obj = new AccountService();
            $res = $obj->UpdatePwd($user_login['userName'], $post['password'],$post['oldPassword']);

            if (!$res) {
                $res = ['code'=>'-102','desc'=>'密码修改失败'];
            }

            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }

        return $this->render('update-pwd',['userData'=>$user_login?:[]]);
    }

    public function actionOpen()
    {
        $account = new AccountService();
        $open_list = $account->SearchOpenList();
        $is_opened = $account->SearchIsOpen();
        if ($is_opened) { //已开户
            $openinfo = $account->searchOpenInfo();
            return $this->render('open2', ['openlist'=>$open_list, 'openinfo'=>$openinfo]);
        }
        return $this->render('open', [ 'openlist'=>$open_list]);
    }

    public function actionAttach()
    {
        $_id = $this->get("id");  //_id
        $gridfs = new GridFsService();
        $fs = $gridfs->getMongoGridFs($_id);
        if (empty($fs)) {
            throw new \yii\base\Exception("没有附件");exit;
        }
        $filename = $fs['filename'];
        $suffix = substr($filename, strrpos($filename, '.')+1);
        if($suffix == "jpg" || $suffix == "jpeg") {
            header('Content-type: image/jpg');
        } elseif($suffix == "bmp") {
            header('Content-type: image/bmp');
        }elseif($suffix == "png") {
            header('Content-type: image/png');
        }elseif($suffix == "pdf") {
            header('Content-type: application/pdf');
            header("Content-Disposition:attachment;filename={$filename}");
        }else{
            header('Content-type: image/jpg');
        }
        echo $fs['file']->toString();exit;
    }

    //未开户提交
    public function actionOpensubmit()
    {
        $args = [];
        $args['extAccount'] = $this->post("productcode");  //账户代码
        $args['extName'] = $this->post("extName");  //账户全称
        $args['operatorPhone'] = $this->post("operatorPhone");  //经办人电话
        $opType = $this->post("opType");  //开户类型

        if ($opType == 1 && (empty($_FILES['organizationCode']['name']) ||empty($_FILES['taxRegistration']['name']))) {
            echo "<script>parent.uploadResult(-1, '必填项不能为空')</script>";
        }
        if (empty($_FILES['operator']['name'])
            || empty($_FILES['productApprovalDoc']['name'])
            || empty($_FILES['bankAccount']['name'])
            ||empty($_FILES['businessLicense']['name'])
            || empty($_FILES['legalPerson']['name'])
            || empty($args['extName'])
            || empty($args['operatorPhone'])) {
            echo "<script>parent.uploadResult(-1, '必填项不能为空')</script>";
        }
        $args['operator'] = $this->uploadtogridfs($_FILES['operator']); //经办人身份证
        $args['productApprovalDoc'] = $this->uploadtogridfs($_FILES['productApprovalDoc']); //产品批复文件
        $args['bankAccount'] = $this->uploadtogridfs($_FILES['bankAccount']); //银行账户文件
        $args['businessLicense'] = $this->uploadtogridfs($_FILES['businessLicense']); //营业执照
        if ($opType == 1) {
            $args['taxRegistration'] =$this->uploadtogridfs($_FILES['taxRegistration']);//税务登记证 -- 借用
            $args['organizationCode'] = $this->uploadtogridfs($_FILES['organizationCode']);//组织机构代码证 -- 借用
        } else{
            $args['taxRegistration'] = "oneinthree";     //税务登记证 -- 借用
            $args['organizationCode'] = "oneinthree";   //组织机构代码证 -- 借用
        }
        $args['legalPerson'] = $this->uploadtogridfs($_FILES['legalPerson']); //法人身份证

        $account = new AccountService();
        $r = $account->AccountOpen($args);
        if ($r) {
            echo "<script>parent.uploadResult(1, '开户成功')</script>";exit;
        } else {
            echo "<script>parent.uploadResult(-1, '您提交的有误')</script>";exit;
        }
    }

    //已开户提交
    public function actionOpen2submit()
    {
        $args = [];
        $args['extAccount'] = $this->post("productcode");  //账户代码
        $args['extName'] = $this->post("extName");  //账户全称
        $args['operatorPhone'] = $this->post("operatorPhone");  //经办人电话

        if (empty($_FILES['operator']['name'])
            || empty($_FILES['productApprovalDoc']['name'])
            || empty($_FILES['bankAccount']['name'])
            || empty($args['extName'])
            || empty($args['operatorPhone'])) {
            echo "<script>parent.uploadResult(-1, '必填项不能为空')</script>";exit;
        }
        $args['operator'] = $this->uploadtogridfs($_FILES['operator']); //经办人身份证
        $args['productApprovalDoc'] = $this->uploadtogridfs($_FILES['productApprovalDoc']); //产品批复文件
        $args['bankAccount'] = $this->uploadtogridfs($_FILES['bankAccount']); //银行账户文件
        $args['businessLicense'] = "";      //营业执照
        $args['taxRegistration'] = "";     //税务登记证 -- 借用
        $args['organizationCode'] = "";   //组织机构代码证 -- 借用
        $args['legalPerson'] = "";      //法人身份证

        $account = new AccountService();
        $r = $account->AccountOpen($args);
        if ($r) {
            echo "<script>parent.uploadResult(1, '开户成功')</script>";exit;
        } else {
            echo "<script>parent.uploadResult(-1, '您提交的有误')</script>";exit;
        }
    }

    //上传文件至mongodb gridfs
    public function uploadtogridfs($file)
    {
        if(empty($file['name'])) {
            return "";
        }
        if ($file['type'] == "image/jpeg" || $file['type'] == "image/jpeg") {
            $suffix = ".jpg";
        } elseif($file['type'] == "image/png") {
            $suffix = ".png";
        } elseif($file['type'] == "image/bmp") {
            $suffix = ".bmp";
        } elseif($file['type'] == "image/bmp") {
            $suffix = ".bmp";
        } else {
            $suffix = substr($file['name'], strrpos($file['name'], '.'));
        }
        $upload = Yii::$app->mongodb->getFileCollection()->createUpload();
        $upload->filename = date("YmdHis").rand(0,10).$suffix;
        $document = $upload->addFile($file['tmp_name'])->complete();
        return (string)$document['_id'];
    }
}