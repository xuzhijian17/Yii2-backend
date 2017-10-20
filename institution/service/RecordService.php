<?php
namespace institution\service;

use Yii;
use institution\service\JavaRestful;
/**
 * 交易查询业务处理
 */
class RecordService
{

    /**
     * 机构持仓数据查询
     * @param $orgCode 机构代码
     * @param string $tradeacco 交易账号，如果为空，则查询所有
     * @param string $order 排序字段
     * @param string $by 排序类型
     * @return array
     */
    public function QueryPosition($orgCode, $tradeacco="", $order="", $by="")
    {
        $params = ['orgCode'=>$orgCode, 'page'=>1, 'rows'=>30];
        if (!empty($tradeacco)) {
            $params['tradeacco'] = $tradeacco;
        }
        if (!empty($order) && !empty($by)) {
            $params['sort'] = $order == "mv" ? 'marketvalue' : 'unpaidincome';
            $params['order'] = $by == "up" ? 'asc' : 'desc';
        }
        $obj = new JavaRestful('S001', $params, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res']['listObjects'])){
            return $res['res']['listObjects'];
        }
        return [];
    }


    public function arrayMultisort($data, $order="", $by="")
    {
        if (empty($data)) {
            return [];
        }
        if (empty($order)) {
            return $data;
        }
        if ($order == 'mv' || $order == 'ui') {
            $key_field = $order == 'mv' ? 'marketvalue' : 'unpaidincome';
            $sort_s = $by == 'up' ? 'SORT_ASC' : 'SORT_DESC';
            $sort = array(
                'direction' => $sort_s, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => $key_field,       //排序字段
            );
            $arrSort = array();
            foreach($data AS $uniqid => $row){
                foreach($row AS $key=>$value){
                    $arrSort[$key][$uniqid] = $key == 'marketvalue' || $key == 'unpaidincome' ? str_replace(',','',$value) : $value;
                }
            }
            if($sort['direction']){
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data);
            }
        }
        return  $data;
    }

    /**
     * 产品查询
     * @param $orgCode
     * @return array
     */
    public function QueryProduct($orgCode)
    {
        $params = [$orgCode];
        $obj = new JavaRestful('S002', $params, 1);
        $res = $obj->apiRequest();
        $result = [];
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])) {
            foreach ($res['res'] as $k=>$v) {
                $v_exp = explode(',', $v);
                $result[$v_exp[0]] = $v_exp[1];
            }
            return $result;
        }
        return $result;
    }

    /**
     *  交易申请查询
     * @param $args
     * @param $page
     * @param $rows
     * @param string $sort
     * @param string $order
     * @return array
     */
    public function QueryTradeApplyList($args=[], $page=1, $rows=10, $sort="", $order="")
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $params = ['orgCode'=>$orgCode, 'page'=>$page, 'rows'=>$rows];
        $params = array_merge($params, $args);
        $obj = new JavaRestful('S003', $params, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res']['listObjects'])){
            $return['page'] = $this->getPager($res['res']['page'], $res['res']['rows'], $res['res']['total']);
            $return['list'] = $res['res']['listObjects'];
            return $return;
        }
        return ['list'=>[]];
    }

    /**
     *  交易确认查询
     * @param $args
     * @param $page
     * @param $rows
     * @param string $sort
     * @param string $order
     * @return array
     */
    public function QueryTradeConfirmList($args=[], $page=1, $rows=10, $sort="", $order="")
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $params = ['orgCode'=>$orgCode, 'page'=>$page, 'rows'=>$rows];
        $params = array_merge($params, $args);
        $obj = new JavaRestful('S004', $params, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res']['listObjects'])){
            $return['page'] = $this->getPager($res['res']['page'], $res['res']['rows'], $res['res']['total']);
            $return['list'] = $res['res']['listObjects'];
            return $return;
        }
        return ['list'=>[]];
    }

    /**
     *  分红查询
     * @param $args
     * @param $page
     * @param $rows
     * @param string $sort
     * @param string $order
     * @return array
     */
    public function QueryTradeBonusList($args=[], $page=1, $rows=10, $sort="", $order="")
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $params = ['orgCode'=>$orgCode, 'page'=>$page, 'rows'=>$rows];
        $params = array_merge($params, $args);
        $obj = new JavaRestful('S005', $params, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res']['listObjects'])){
            $return['page'] = $this->getPager($res['res']['page'], $res['res']['rows'], $res['res']['total']);
            $return['list'] = $res['res']['listObjects'];
            return $return;
        }
        return ['list'=>[]];
    }
    //获取分页
    public function getPager($page, $rows, $total)
    {
        $rows = $rows<=0 ? $total : $rows;
        $pager = ['page'=> $page, 'rows'=> $rows, 'total'=> $total];
        $pager['start'] = (($page-1)*$rows) + 1;
        $pager['pagecount'] = intval(ceil($total/$rows));
        if ($page*$rows > $total) {
            $pager['end'] = $total;
        } else {
            $pager['end'] = $page*$rows;
        }
        return $pager;
    }
}