<?php
namespace frontend\modules\api\services;
use frontend\modules\api\models\ChangeCardLog;
use frontend\modules\api\models\UserBank;
use frontend\modules\api\models\BankInfo;
use Yii;
use common\lib\CommFun;
use frontend\services\TradeService;
use frontend\models\User;
use common\lib\HundSun;

/**
 * 银行卡账户类
 * 包含：换卡的业务处理，修改银行卡资料业务处理、修改账户密码业务处理、问卷等
 */
class AccountServiceApi extends TradeService
{
    /*
     * 构造方法
     * @param int $uid 用户id
     * @param int $merid 商户号
     * @param string $fundCode 基金代码
     */
    public function __construct ( $uid, $merid )
    {
        parent::__construct ( $uid, $merid, "" );
    }

    /*
     * 处理银行卡换卡业务处理
     * @param $param
     */
    public function changeBankCard ( $param )
    {
        $user_obj = new User();
        $user_info = $user_obj->getUserInfoByUid($this->uid);
        if (empty($user_info)) {
            CommFun::handleCode('-7');
        }
        $user_bank = UserBank::getUserBankByBankAcco($param['old_bankacco']);
        if (empty($user_bank)) {
            CommFun::handleCode('-200');
        }else {
            $user_bank['Authorization'] ==1 or CommFun::handleCode(-207);
        }

        //组装接口参数
        $B016 = [];

        $B016['busitype'] = "CHANGEBINDCARD"; //更换银行卡
        $B016['detailcapitalmode'] = "01"; //资金明细方式
        $B016['bankacco'] = $param['bankacco']; //修改后银行卡号
        $B016['bankacconame'] = $user_info['Name']; //用户真实姓名
        $B016['bankserial'] = $param['bankserial']; //银行编号
        $B016['brachbank'] = $param['branchbank'];    //联行号
        $B016['capitalmode'] = 'P'; //资金方式
        $B016['identitytype'] = "0"; //证件类型
        $B016['identityno'] = $user_info['CardID']; //身份证号
        $B016['tradeacco']  = $user_bank['TradeAcco']; //交易账号
        $new_bank_info = BankInfo::getBankQuotaInfo($param['bankserial']);
        $B016['bankname'] = empty($new_bank_info['BankName'])?'未知':$new_bank_info['BankName'];//银行名称

        $resB016 = $this->obj_hs->apiRequest('B016', $B016);

        if ($resB016['code'] == parent::HS_SUCC_CODE) { //在本地库记录
            $bank_info = BankInfo::getBankQuotaInfo($user_bank['BankSerial']);
            //保存换卡历史记录
            $model = new ChangeCardLog();
            $model->Uid = $this->uid;
            $model->OldBankSerial = $user_bank['BankSerial'];
            $model->OldBankName = !empty($bank_info['BankName']) ? $bank_info['BankName'] : '';
            $model->OldBankAcco = $user_bank['BankAcco'];
            $model->OldBindTime = $user_bank['BindTime'];
            $model->NewBankSerial = $param['bankserial'];
            $model->NewBankName = $B016['bankname'];
            $model->NewBankAcco = $param['bankacco'];
            $model->NewBindTime = date("Y-m-d H:i:s");
            $model->save();
            //更新旧卡信息
            $user_bank['BankAcco'] = $param['bankacco'];
            $user_bank['BankSerial'] = $param['bankserial'];
            $user_bank['BindTime'] = $model->NewBindTime;
            $user_bank['Authorization'] = 0;
            UserBank::updateAll($user_bank, ['id'=>$user_bank['id']]);
        }
        return $resB016;
    }
    /**
     * 登录交易系统
     * @param string $password 密码 可空
     * @param string $lognumber 证件号 可空
     * @param string $uid 用户id 可空
     */
    public function HandleLogin($password=null,$identityno=null,$uid=null)
    {
        if (empty($uid)){
            if (empty($password) || empty($identityno)){
                CommFun::handleCode('-3');//缺少必要参数
            }
            $user = Yii::$app->db_local->createCommand("SELECT * FROM `user` WHERE CardID = '{$identityno}'")->queryOne();
            if (empty($user))
            {
                CommFun::handleCode('-202');//登陆失败，身份证号错误
            }else {
                if (CommFun::AutoEncrypt($user['Pass'],'D') != $password){
                    CommFun::handleCode('-203');//登陆失败，密码错误
                }
            }
        }else {
            $user = Yii::$app->db_local->createCommand("SELECT * FROM `user` WHERE id = '{$uid}' and Instid = '{$this->merid}' ")->queryOne();
            if (empty($user)){
                CommFun::handleCode('-7');//无此uid
            }
            $password = CommFun::AutoEncrypt($user['Pass'],'D');
            $identityno = $user['CardID'];
        }
        $privP003 = ['certificatetype'=>'0','password'=>$password,'lognumber'=>$identityno,'logtype'=>'2'];
        $resP003 = $this->obj_hs->apiRequest('P003',$privP003);
        if ($resP003['code'] == parent::HS_SUCC_CODE && !empty($resP003['sessionkey']))
        {
            $redis = Yii::$app->redis;
            $redis->set('sessionkey_'.$user['id'],$resP003['sessionkey']);
            $redis->expire('sessionkey_'.$user['id'],1200);
        }
        return $resP003;
    }
    /**
     * 检验此身份证号是否已开户
     * @param string $identityno 身份证号
     */
    public static function CheckIdCard($identityno)
    {
        $user = Yii::$app->db_local->createCommand("SELECT * FROM `user` WHERE CardID = '{$identityno}'")->queryOne();
        if (!empty($user))
        {
            CommFun::handleCode('-204');//用户已开户
        }
    }
}