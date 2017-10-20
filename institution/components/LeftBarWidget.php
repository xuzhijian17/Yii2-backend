<?php
namespace institution\components;

use Yii;
use yii\base\Widget;

class LeftBarWidget extends Widget
{
    public $user=[];//用户session
    public $menu=[];//导航菜单
    //重写init 初始化熟悉
    public function init()
    {
        parent::init();
        $this->user = Yii::$app->session['user_login'];
        $url = Yii::$app->request->getPathInfo();
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        $url = trim($url, '/');
        $this->menu=[
            ['url'=>'/record/position','menu'=>'持仓查询','class'=>('record/position'==$url)?'cur':''],
            ['url'=>'/fund/index','menu'=>'产品市场','class'=>('fund/index'==$url)?'cur':''],
            ['url'=>'/trade/orderpage','menu'=>'交易下单','class'=>('trade/orderpage'==$url)?'cur':''],
            ['url'=>'/record/order','menu'=>'交易查询','class'=>('record/order'==$url)?'cur':''],
            ['url'=>'/message/index','menu'=>'消息中心','class'=>('message/index'==$url)?'cur':''],
            ['url'=>'/account/open','menu'=>'机构开户','class'=>('account/open'==$url)?'cur':''],
            ['url'=>'/account/update-pwd','menu'=>'设置密码','class'=>('account/update-pwd'==$url)?'cur':''],
        ];
    }
    //渲染
    public function run()
    {
        return $this->render('@institution/views/widget/leftbar',['user'=>$this->user,'menu'=>$this->menu]);
    }
}