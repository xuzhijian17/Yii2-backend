<?php

namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\HundSun;

/**
 * This is the model class for FundAccount.
 *
 */
class FundAccount extends Model
{
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
     * @inheritdoc
     */
    public static function getDb($db_name='db_juyuan')
    {
        return $db_name ? Yii::$app->$db_name : Yii::$app->db;
    }

    public static function getAccountInfo($uid)
    {
        $oHundSun = new HundSun($uid);

        $priv = ['custtype'=>1];
        $userInfo = $oHundSun->apiRequest('C010',$priv);

        if (isset($userInfo['code']) && $userInfo['code'] == 'ETS-5BP0000') {
            switch ($userInfo['riskability']) {
                case 1:
                    $userInfo['riskabilityStr'] = '安全型';
                    break;
                case 2:
                    $userInfo['riskabilityStr'] = '保守型';
                    break;
                case 3:
                    $userInfo['riskabilityStr'] = '稳健型';
                    break;
                case 4:
                    $userInfo['riskabilityStr'] = '积极型';
                    break;
                case 5:
                    $userInfo['riskabilityStr'] = '进取型';
                    break;
                default:
                    $userInfo['riskabilityStr'] = '保守型';
                    break;
            }
        }else{
            $userInfo = [];
        }

        // $default_fundacco = '441100007980';
        // var_dump($userInfo['fundacco']);
        $bankInfo = [];
        $fundacco = explode(',', isset($userInfo['fundacco']) ? $userInfo['fundacco'] : '');
        foreach ($fundacco as $key => $value) {
            $priv = ['fundacco'=>$value];
            $tradeacco = $oHundSun->apiRequest('C020',$priv);

            if (isset($tradeacco['code']) && $tradeacco['code'] == 'ETS-5BP0000') {
                if (isset($tradeacco['tradeaccolist'])) {
                    foreach ($tradeacco['tradeaccolist'] as $key => $value) {
                        if ($value['maintradeacco'] && in_array($value['state'], ['0','1']) && in_array($value['fundaccostate'], ['0','1','2'])) {
                            // 这里只取了一个,待讨论
                            $bankInfo = $value;
                        }
                    }
                }
            }
        }
        
        return array_merge($userInfo, $bankInfo);
    }


    /**
     * Get account assets data and confirm fund list data
     */
    public static function accountAssets($uid)
    {
        $oHundSun = new HundSun($uid);
        // 持有中数据（基金份额查询）
        $priv = ['filterzerofundshare'=>'1','querytype'=>'1'];
        $rssharequery = $oHundSun->apiRequest('S001',$priv);
        
        // 总金额
        $fundAccountData['totalAssets'] = isset($rssharequery['totalcapital']) ? $rssharequery['totalcapital'] : '0.00';

        $totaldayincomesum = 0;
        $totalincomesum = 0;
        $confirmlist = isset($rssharequery['returnlist']) ? $rssharequery['returnlist'] : [];
        foreach ($confirmlist as $key => &$value) {
            $value['dayincome'] = sprintf("%01.2f", $value['dayincome']);
            $value['totalincome'] = sprintf("%01.2f", $value['totalincome']);
            $value['marketvalue'] = sprintf("%01.2f", $value['marketvalue']);
            $totaldayincomesum += $value['dayincome'];
            $totalincomesum += $value['totalincome'];
            
            // 获取分红日（取权益登记日）
            $sql = "SELECT d.ReDate FROM SecuMain s INNER JOIN MF_Dividend d ON s.InnerCode=d.InnerCode AND s.SecuCategory=8 WHERE s.SecuCode=".$value['fundcode']." ORDER BY d.ReDate DESC LIMIT 1";
            $command = self::getDb()->createCommand($sql);
            $value['redate'] = $command->queryScalar();
        }

        
        $fundAccountData['totaldayincomesum'] = sprintf("%01.2f", $totaldayincomesum);    // 持仓昨日收益
        $fundAccountData['totalincomesum'] = sprintf("%01.2f", $totalincomesum);      // 持仓累计收益
        $fundAccountData['confirmlist'] = $confirmlist;    // 持仓基金列表

        return $fundAccountData;
    }

    /**
     * Get trade fund list data
     */
    public static function tradelist($uid)
    {
        $_oHundSun = new HundSun($uid);
        // 处理中数据（交易申请查询）
        $priv = ['applyrecordno'=>'150'];
        $rstradelist = $_oHundSun->apiRequest('S003',$priv);
        $tradelist = isset($rstradelist['returnlist']) ? $rstradelist['returnlist'] : [];    // clone 一份
    
        // 过滤不同状态的多条基金记录只显示一条
        $tradefundcode = [];
        foreach ($tradelist as $key => &$value) {
            // 过滤非处理中和多条同一只基金的处理中数据
            if (!in_array([$value['fundcode'],$value['callingcode']], $tradefundcode)) {
                $value['tradefundnum'] = 1;     //设置默认交易笔数
                $tradefundcode[] = [$value['fundcode'],$value['callingcode']];
            }else{
                unset($tradelist[$key]);
                continue;
            }
            
            // 只保留申购、赎回、认购处理中的数据
            if ($value['confirmflag'] == '9' && ($value['callingcode'] == '022' || $value['callingcode'] == '024' || $value['callingcode'] == '020')) {
                $value['confirmstat'] = '待确认';  // 修改确认状态文字
            }else{
                unset($tradelist[$key]);
                continue;
            }


            // 格式化输出处理中的数据，priceflagStr为动态自定义添加
            if ($value['callingcode'] == '022') {
                $value['businflagStr'] = '买入';
                $value['priceflagStr'] = '待确认金额';
                $value['applyval'] = $value['applysum'];
            }elseif ($value['callingcode'] == '024') {
                $value['businflagStr'] = '卖出';
                $value['priceflagStr'] = '赎回份额';
                $value['applyval'] = $value['applyshare'];
            }elseif ($value['callingcode'] == '020') {
                $value['businflagStr'] = '认购';
                $value['priceflagStr'] = '待确认金额';
                $value['applyval'] = $value['applyshare'];
            }else{
                // $value['businflagStr'] = '买入';
                $value['priceflagStr'] = '待确认金额';
                $value['applyval'] = $value['applysum'];
            }
        }
        
        // 计算处理中的交易笔数、累计金额、累计份额
        foreach ($tradelist as $key => &$value) {
            $tradefundnum = 0;
            $totalapplysum = 0;
            foreach ($rstradelist['returnlist'] as $k => $v) {

                // 只保留申购、赎回、认购处理中的数据
                if ($v['confirmflag'] == '9' && ($v['callingcode'] == '022' || $v['callingcode'] == '024' || $v['callingcode'] == '020')) {
                    $v['confirmstat'] = '待确认';  // 修改确认状态文字
                }else{
                    unset($rstradelist['returnlist'][$k]);
                    continue;
                }

                // 开始匹对
                if ($v['fundcode'] == $value['fundcode'] && $v['callingcode'] == $value['callingcode']) {
                    // 累计处理中的交易金额、赎回份额
                    if ($v['callingcode'] == '022') {
                        $totalapplysum += $value['applysum'];
                    }elseif ($value['callingcode'] == '024') {
                        $totalapplysum += $value['applyshare'];
                    }else{
                        $totalapplysum += $value['applysum'];
                    }

                    // 累计处理中的交易笔数
                    $tradefundnum += 1;
                }
            }
            $value['tradefundnum'] = $tradefundnum;
            $value['applyval'] = $totalapplysum;
        }

        return $tradelist;
    }
}
