<?php
namespace frontend\controllers;

use Yii;
use common\lib\CommFun;
use yii\web\Session;

class SandboxController extends \yii\web\Controller
{
    public $sandboxUrl = '';
    public $session;
    public function init()
    {
       $this->session = Yii::$app->session;

        $this->sandboxUrl = Yii::$app->params['sandboxUrl'];//sandbox请求地址
    }
    public function actionLoginpage()
    {
        return $this->renderPartial('login');
    }
    public function actionDologin()
    {
        $username = Yii::$app->request->post('username','');//用户名
        $password = Yii::$app->request->post('password','');//密码
        if ($username == 'hc_developer007' && $password =='2016hc1108htg1qaz') {
            $this->session->set('hcjj_dev', time());
            return $this->runAction('index');
        }else {
            echo '账号输入错误';
            exit();
        }
    }
    public function actionDologinout()
    {
        $this->session->remove('hcjj_dev');
        return $this->runAction('loginpage');
    }
    public function actionIndex()
    {
        if (empty($this->session->get('hcjj_dev'))){
            return $this->runAction('loginpage');
        }else {
            $data = self::getApiArr('account');
            if (empty($data)) {
                exit('参数为空');
            }
            return $this->renderPartial('index',['apidata'=>$data,'module'=>'account','admin'=>$this->session->get('hcjj_dev')]);
        }
    }

    /**
     * 根据模块选择功能
     */
    public function actionChoseapi()
    {
        $module = \Yii::$app->request->post('module');
        $data = self::getApiArr($module);
        if (empty($data)) {
            exit('参数为空');
        }
        return $this->renderPartial('index',['apidata'=>$data,'module'=>$module]);
    }

    /**
     * 获取模块-功能对应数据
     * @param string $module 模块id
     * @return array 此模块下所有的api
     */
    private static function getApiArr($module)
    {
       $conf = [
            //账户类
           'account'=>[
               'login'=>['instid','signmsg','password','identityno','hcid'],
               'banksendauthcode'=>['instid','version','signmsg','bankacco','customername','orderno','identityno','mobile', 'bankserial'],
               'bankverifyauthcode'=>['instid','version','signmsg','bankacco','accoreqserial','orderno','identityno','mobile','mobileauthcode','customername','otherserial', 'bankserial'],
               'meropenacco'=>['instid','version','signmsg','orderno','bankacco','bankname','bankserial','brachbank','customerappellation','identityno','tradepassword','mobile','yinliancdcard'],
// 			   'getuserinfo'=>['instid','version','signmsg','hcid'],
			   'getriskinfo'=>['instid','version','signmsg','hcid'],
               'modifyriskinfo'=>['instid','version','signmsg','hcid','identityno','qanswer','qno'],
               'modifyrisklevel'=>['instid','version','signmsg','hcid','riskability'],
               'modifypwd'=>['instid','version','signmsg','hcid','newpwd','oldpwd'],
               'getbrachbank'=>['instid','version','signmsg','type','pid','cid','bankno'],
           ],
           //交易类
           'trade'=>[
               'purchase'=>['instid','version','signmsg','hcid','orderno','bankacco','applysum','tradepassword','fundcode'],
               'withdraw'=>['instid','version','signmsg','hcid','orderno','tradepassword','applyserial'],
               'sale'=>['instid','version','signmsg','hcid','orderno','bankacco','applyshare','tradepassword','fundcode'],
               'valutrade'=>['instid','version','signmsg','hcid','orderno','bankacco','tradepassword','fundcode','applysum','cycleunit',
                   'jyrq','zzrq','scjyrq','jyzq'
               ],
               'valutradechange'=>['instid','version','signmsg','hcid','tradepassword','xyh','applysum','cycleunit','jyrq',
                   'zzrq','jyzq','state'
               ],
               'bonus'=>['instid','version','signmsg','hcid','tradepassword','fundcode','melonmethod','orderno']
           ],
           //基金超市类
           'fund'=>[
                'index'=>['instid','version','signmsg','fundtype'],
                'detail'=>['instid','version','signmsg','fundcode'],
                'nv-chart'=>['instid','version','signmsg','fundcode','startday'],
                'charge-rate'=>['instid','version','signmsg','fundcode','chargeratetype'],
                'charge-rate-new'=>['instid','version','signmsg','fundcode','chargeratetype'],
                'manager'=>['instid','version','signmsg','fundcode'],
                'archives'=>['instid','version','signmsg','fundcode'],
                'investment-portfolio'=>['instid','version','signmsg','fundcode'],
                'bulletin'=>['instid','version','signmsg','fundcode','type','pageno','applyrecordno'],
                'balance-sheet'=>['instid','version','signmsg','fundcode'],
                'history-nv'=>['instid','version','signmsg','fundcode','startday','endday'],
           ],
           //自定义基金列表类
           'fund-v2'=>[
                'index'=>['instid','version','signmsg','cid'],
                'categorys'=>['instid','version','signmsg'],
                // 'themes'=>['instid','version','signmsg'],
           ],
           //查询类
           'query' => [
               'sharequery' => ['instid', 'version', 'signmsg', 'hcid', 'fundcode'],
//                'tradeapply' => [
//                                 'instid', 'version', 'signmsg', 'hcid', 'applyrecordno', 'pageno',
//                                 'startdate', 'enddate', 'applyserial', 'xyh'
//                                 ],
//                'confirmquery' => [
//                                'instid', 'version', 'signmsg', 'hcid', 'applyrecordno', 'pageno', 'fundcode',
//                                'callingcode', 'startdate', 'enddate', 'requestno', 'xyh'
//                                 ],
               'traderecord' => [
                   'instid', 'version', 'signmsg', 'hcid', 'applyrecordno', 'pageno','orderno',
                   'startdate', 'enddate', 'applyserial', 'xyh','nowithdraw'
               ],
               'hisbonuslist'=>['instid','signmsg','hcid','fundcode','applyrecordno','pageno','startdate', 'enddate'],
               'getorderstatus'=>['instid','signmsg','orderno'],
               'getlimitinfo'=>['instid','signmsg','fundcode'],
               'funddividend'=>['instid','signmsg', 'fundcode'],
           ],
           //现金宝类
           'bao'=>[
//                'purchase' => [
//                            'instid', 'version', 'signmsg', 'hcid', 'orderno', 'bankacco', 'applysum',
//                            'tradepassword','fundcode'
//                             ],
//                'withdraw' => ['instid', 'version', 'signmsg', 'hcid', 'orderno', 'tradepassword', 'applyserial'],
               'sale' => [
                        'instid', 'version', 'signmsg', 'hcid', 'orderno', 'bankacco',
                        'applyshare', 'tradepassword', 'fundcode'
                        ],
           ],
           //银行卡类
           'bankcard' => [
               'changebankcard' => [    //变更银行卡
                                   'instid', 'version', 'signmsg', 'hcid', 'old_bankacco', 'bankacco', 'bankname',
                                   'bankserial','branchbank'
                                   ],
               'tradequotainfo' => ['instid', 'version', 'signmsg', 'bankserial'], //银行卡限额详情
               'authtransfer'=>['instid','signmsg','customername','bankacco','bankno','branchbank','identityno'],//一键迁移
           ],
           //组合交易类
           'portfolio'=>[
               'portfoliolist' => ['instid', 'signmsg'],
               'purchase' => ['instid', 'version', 'signmsg', 'hcid', 'orderno', 'bankacco',
                                'applysum', 'tradepassword', 'portfolioid'
                                ] ,
               'withdraw' => ['instid','version','signmsg','hcid','orderno','tradepassword','portfoliotradeid', 'portfolioid'],
               'sale' => ['instid','version','signmsg','hcid','orderno','bankacco','tradepassword', 'portfolioid', 'ratio'],
               'traderecord' => ['instid', 'version', 'signmsg', 'hcid', 'applyrecordno', 'pageno', 'startdate', 'enddate','portfoliotradeid'],
           ],
       ];
       return empty($conf[$module])?false:$conf[$module];
    }
    /**
     * 获取模块功能对应字段 
     */
    public function actionGetparam()
    {
        $module = \Yii::$app->request->post('module');
        $api = \Yii::$app->request->post('api');
        $data = self::getApiArr($module);
        if (empty($data[$api])) {
            exit('参数为空');
        }
        return $this->renderPartial('index',['apidata'=>$data,'api'=>$api,'module'=>$module,'param'=>$data[$api]]);
    }
    /**
     * 生成签名字符串
     */
    public function actionGetsignature()
    {
        $post = \Yii::$app->request->post();
        unset($post['signmsg'],$post['url']);
        if (!isset($post['instid'])){
            exit(json_encode(['code'=>'1','data'=>'未填写商户编号']));
        }
        $partner = CommFun::getPartnerInfo($post['instid']);
        if (empty($partner))
        {
            exit(json_encode(['code'=>'1','data'=>'配置未定义商户编号']));
        }
        $secretkey = isset($partner['PassWord'])?$partner['PassWord']:exit(json_encode(['code'=>'1','data'=>'未定义商户秘钥']));
        if(is_array($post) && !empty($post))
        {
            ksort($post);
            $tokenStr = $secretkey;
            foreach($post as $k=>$v)
            {
                if(!empty($v) || $v == '0')
                {
                    $tokenStr .= $k.$v;
                }
            }
            $tokenStr .= $secretkey;
            echo json_encode(['code'=>'0','data'=> strtoupper(md5($tokenStr))]);
        }else {
            echo json_encode(['code'=>'1','data'=>'提交数据不正确']);
        }
    }
    /**
     * 生成订单号
     */
    public function actionGetorderno()
    {
        $orderno = CommFun::getOrderNo(rand(1,100));
        echo $orderno;
    }
    /**
     * api提交
     */
    public function actionApipost()
    {
        $post = \Yii::$app->request->post();
        $base = empty($post['url'])?exit('提交地址错误'):'/api/'.$post['url'];
        unset($post['url']);
       echo $this->curlPost($this->sandboxUrl.$base,$post);
    }
    public function curlPost($base, $params, $is_json = 0)
    {
        $post_string = '';
        if($is_json){
            $post_string = $params;//json 格式
        }else{
            if (!empty($params)) {
                $post_string = http_build_query($params);
            }
        }
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $base,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
    
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_string,
        );
        curl_setopt_array($ch, $options);
        if($is_json){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8;','Content-Length: ' . strlen($post_string)) );
        }
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }
}