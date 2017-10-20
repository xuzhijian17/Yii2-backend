<?php
namespace frontend\modules\api\services;

use Yii;
/**
 * 银行卡类业务处理类
 *
 */
class BankCardServiceApi
{
    /**
     * 鉴权迁移功能(易宝支付用)
     * @param array $param ['customername'=>'客户姓名','identityno'=>'证件号码',
     * 'bankacco'=>'银行卡号','brachbank'=>'联行号','bankno'=>'银行机构号']
     * @return array ['code'=>'返回码','message'=>'返回信息']
     */
    public function HandleAuthTransfer($param)
    {
        $yeepay = Yii::$app->params['yeepay'];
        $senddate = date('Ymd');
        $sendtime = date('His');
        $sendseq = $senddate.substr(time(),-5).substr(microtime(),2,7);
        $grp = "<Grp><GrpHead><Version>2.1.0</Version><BusCd>1006</BusCd>"
            ."<MctCd>".$yeepay['mctid']."</MctCd><SendDate>{$senddate}</SendDate><SendTime>{$sendtime}"
            ."</SendTime><SendSeq>".$sendseq."</SendSeq></GrpHead><GrpBody><CstName>".$param['customername']
            ."</CstName><CertType>"."010"."</CertType><CertCode>".$param['identityno']."</CertCode><BankNo>{$param['bankno']}</BankNo><UnionBankNo>"
            .$param['brachbank']."</UnionBankNo><CstCard>" .$param['bankacco']."</CstCard>"
            ."<Remark></Remark></GrpBody></Grp>";
        $sign = $this->YeepaySign($grp, $yeepay['privateKeyFile'], $yeepay['password']);
        $requestxml = "<MsgTes>{$grp}<Sign>{$sign}</Sign></MsgTes>";
        $responsexml = base64_decode($this->curlPost($yeepay['url'], ['data'=>base64_encode($requestxml)]));
        $objxml = simplexml_load_string($responsexml);
        if ($objxml)
        {
            if (isset($objxml->Grp->GrpBody->RSTCode) && $objxml->Grp->GrpBody->RSTCode=='000000')
            {
                //验签
                if ($this->VerityForYeepay($responsexml,Yii::$app->params['publicKeyFile']))
                {
                    $resArr['code'] = 'ETS-5BP0000';//符合hundsun接口
                    $resArr['message'] = isset($objxml->Grp->GrpBody->RSTMsg)?(string)$objxml->Grp->GrpBody->RSTMsg:'';
                    $resArr['customername'] = isset($objxml->Grp->GrpBody->CstName)?(string)$objxml->Grp->GrpBody->CstName:'';
                    $resArr['bankacco'] = isset($objxml->Grp->GrpBody->CstCard)?(string)$objxml->Grp->GrpBody->CstCard:'';
                    $resArr['yinliancdcard'] = isset($objxml->Grp->GrpBody->CstAccNo)?(string)$objxml->Grp->GrpBody->CstAccNo:'';
                }else {
                    $resArr['code'] = 'yeepay-001';//符合hundsun接口
                    $resArr['message'] = '支付渠道验签失败';
                }
            }else {
                $msg = isset($objxml->Grp->GrpBody->RSTMsg)?$objxml->Grp->GrpBody->RSTMsg:'支付渠道未知错误';
                $resArr['code'] = 'yeepay-002';//符合hundsun接口
                $resArr['message'] = $msg;
            }
        }else {
            $resArr['code'] = 'yeepay-003';//符合hundsun接口
            $resArr['message'] = '支付渠道通讯超时';
        }
        return $resArr;
    }
    /**
     * 签名加密函数(易宝接口用)
     * @param string $data 加密数据
     * @param string $privatekeyFile 秘钥地址
     * @param string $passphrase 密码
     */
    public function YeepaySign($data, $privatekeyFile,$passphrase)
    {
        $signature = '';
        $privatekey = openssl_pkey_get_private(file_get_contents($privatekeyFile), $passphrase);
        $res=openssl_get_privatekey($privatekey);
        openssl_sign($data, $signature, $res);
        openssl_free_key($res);
        return base64_encode($signature);
    }
    /**
     * curl post提交 (看使用情况与HundSun类下统一使用)
     * @param string $base
     * @param array $params
     * @return mixed
     */
    public function curlPost($base, $params)
    {
        $post_string = '';
        if (!empty($params)) {
            $post_string = http_build_query($params);
        }
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $base,
            CURLOPT_TIMEOUT => 7,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_string,
        );
        curl_setopt_array($ch, $options);
        $rs = curl_exec($ch);
        $error = ($rs===false)?curl_error($ch):'';
        curl_close($ch);
        Yii::info("request:url=>{$base}; params=>".json_encode($params)." | response:{$rs} | time:".round(microtime(true)-$t1,4).'|error:'.$error,__METHOD__);
        return $rs;
    }
    /**
     * 易宝验签函数
     * @param string $xml 返回xml
     * @param string $publicKeyPath 公钥地址
     * @return bool 成功:true/失败:false
     */
    public function VerityForYeepay($xml,$publicKeyPath)
    {
        $grpStart = strpos($xml, '<Grp>');
        $grpEnd = strpos($xml, '</Grp>')+6;
        $grp = substr($xml, $grpStart,$grpEnd-$grpStart);
        $signStart = strpos($xml, '<Sign>')+6;
        $signEnd = strpos($xml, '</Sign>');
        $sign = substr($xml, $signStart,$signEnd-$signStart);
        $verity = $this->RsaVerity($grp, $sign, $publicKeyPath);
        if ($verity)
        {
            return true;
        }else {
            //验签失败 日志
            Yii::error('验签失败xml:'.$xml,__METHOD__);
            return false;
        }
    }
    /**
     * 验证签名(通用)
     * @param string $data 原文
     * @param string $signature 签名
     * @param string $publicKeyPath 公钥地址
     */
    public function RsaVerity($data, $signature, $publicKeyPath)
    {
        $pubKey = file_get_contents($publicKeyPath);
        $res = openssl_get_publickey($pubKey);
        $result = openssl_verify($data, base64_decode($signature), $res);
        openssl_free_key($res);
        return $result;
    }
}