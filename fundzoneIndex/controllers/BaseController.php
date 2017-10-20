<?php
namespace fundzone\controllers;

use yii\filters\AccessControl;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;
    public $request; //request属性获取组件 Yii::$app->request
    public $user; //user属性获取$session['user_login']
    
    public function behaviors()
    {
        return [
            //在only actions中添加登录才能访问的action(注意别重名)
            'access'=>[
                'class'=>AccessControl::className(),
                'denyCallback'=> function ($rule,$action){
                    $this->isLogin();
                },
                'only'=>['company-purchase-page','company-purchase','sell-page','company-sell','withdraw','list','detail','assets','everyday','loginout','changepassword'],
                    'rules'=>[
                        [
                            'actions'=>[
                                    'company-purchase-page','company-purchase','sell-page','company-sell','withdraw','list','detail','assets','everyday','loginout','changepassword'
                            ],
                            'allow'=>true,
                            'roles'=>['@'],
                        ],
                    ],
              ],
          ];
    }
    
    public function init()
    {
        $this->request = Yii::$app->request;
    }

    public function get($param = null,$default = null)
    {
        return $this->request->get($param,$default);
    }
    public function post($param = null,$default = null)
    {
        return $this->request->post($param,$default);
    }
    public function isPost()
    {
        return $this->request->getIsPost();
    }
    public function isGet()
    {
        return $this->request->getIsGet();
    }
    public function isAjax()
    {
        return $this->request->getIsAjax();
    }
    /**
     * 判断是否登录1.未登陆跳转/2已登陆获取session
     * 注:session['user_login']=>['id'=>'用户id','CardID'=>'身份证号','Name'=>'姓名','Instid'=>'商户号',
     * 'AccountStatus'=>'账户状态','OpenStatus'=>'开户状态']
     */
    public function isLogin()
    {
        $session = Yii::$app->session;
        if(empty($session['user_login'])){
//             $referer_url = $this->request->getHostInfo().$this->request->url;
//             setcookie('referer_url', $referer_url, time()+600);//登陆后跳转url
            $url = Yii::$app->getUrlManager()->createUrl('company/login');
            return $this->redirect($url);
        }else {
            $this->user = $session['user_login'];
        }
    }
    /**
     * 跳转错误页面
     */
    public function errPage($msg)
    {
        return $this->render('/base/error',['data'=>$msg]);
    }

    function get_pager($url, $param, $record_count, $page = 1, $size = 10)
    {
        $size = intval($size) < 1 ? 10 : intval($size);
        $page = intval($page) < 1 ? 1 : intval($page);

        $record_count = intval($record_count);

        $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
        if ($page > $page_count){
            $page = $page_count;
        }

        $page_prev  = ($page > 1) ? $page - 1 : 1;
        $page_next  = ($page < $page_count) ? $page + 1 : $page_count;

        /* 将参数合成url字串 */
        $param_url = '?';
        foreach ($param AS $key => $value){
            $param_url .= $key . '=' . $value . '&';
        }

        $pager['url']          = $url;
        $pager['start']        = ($page -1) * $size;
        $pager['page']         = $page;
        $pager['size']         = $size;
        $pager['record_count'] = $record_count;
        $pager['page_count']   = $page_count;

        $_pagenum = 10;     // 显示的页码
        $_offset = 2;       // 当前页偏移值
        $_from = $_to = 0;  // 开始页, 结束页
        if($_pagenum > $page_count){
            $_from = 1;
            $_to = $page_count;
        }else{
            $_from = $page - $_offset;
            $_to = $_from + $_pagenum - 1;
            if($_from < 1){
                $_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum){
                    $_to = $_pagenum;
                }
            }elseif($_to > $page_count){
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
        }
        $pager['page_url'] = $url . $param_url;
        $url_format = $url . $param_url . 'page=';
        $pager['page_first']   = $url . $param_url . 'page=1';
        $pager['page_prev']    = $url . $param_url . 'page=' . $page_prev;
        $pager['page_next']    = $url . $param_url . 'page=' . $page_next;
        $pager['page_last']    = $url . $param_url . 'page=' . $page_count;
        $pager['page_number'] = array();
        for ($i=$_from;$i<=$_to;++$i){
            $pager['page_number'][$i] = $url_format . $i;
        }
        return $pager;
    }
}