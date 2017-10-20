<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Left Menu Widget
 * @author Xuzhijian17
 */
class LeftMenu extends Widget
{
    public $menuName = '';

    public $uid;
    public $instid;
    public $type;

    public $curSubMenuList = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {

        if ($this->menuName == 'maintain') {
            $leftMenu = $this->maintain();
        }elseif ($this->menuName == 'position') {
            $leftMenu = $this->position();
        }elseif ($this->menuName == 'statistics') {
            $leftMenu = $this->statistics();
        }elseif ($this->menuName == 'moneyfund') {
            $leftMenu = $this->moneyfund();
        }elseif ($this->menuName == 'userinfo') {
            $leftMenu = $this->userinfo();
        }else{
            $leftMenu = '';
        }
		
		return $leftMenu;
    }

    public function userinfo($value='')
    {
        $html = '';
        $html .= '<aside><div class="subNav">';
        foreach (Yii::$app->admin->getSubmenus(1) as $key => $value) {
            $html .= '<a href="'.Url::to([$value['Route'],'uid'=>$this->uid,'instid'=>$this->instid]).'" class="subLink '.(Yii::$app->request->pathInfo == $value['Route'] ? 'subCur' : '').'">'.$value['Flag'].'</a>';
        }
        /*$html .= '<a href="'.\yii\helpers\Url::to(['user/detail','uid'=>$this->uid,'instid'=>$this->instid]).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('user/detail') ? 'subCur' : '').'">基本信息</a>';
        $html .= '<a href="'.\yii\helpers\Url::to(['user/user-bank','uid'=>$this->uid,'instid'=>$this->instid]).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('user/user-bank') ? 'subCur' : '').'">银行卡信息</a>';
        $html .= '<a href="'.\yii\helpers\Url::to(['user/change-bank','uid'=>$this->uid,'instid'=>$this->instid]).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('user/change-bank') ? 'subCur' : '').'">换卡记录</a>';*/
        $html .= '</div></aside>';

        return $html;
    }
    
    public function position($value='')
    {
        $html = '';
        $html .= '<aside><div class="subNav">';
        $html .= '<a href="'.\yii\helpers\Url::to(['position/index','uid'=>$this->uid,'instid'=>$this->instid,'type'=>$this->type]).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('position/index') ? 'subCur' : '').'">基金持仓</a>';
        
        if ($this->type == 0) {
            $html .= '<a href="'.\yii\helpers\Url::to(['position/cast-surely-agreement','uid'=>$this->uid,'instid'=>$this->instid,'type'=>$this->type]).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('position/cast-surely-agreement') ? 'subCur' : '').'">定投协议</a>';
        }
        
        $html .= '</div></aside>';

        return $html;
    }

    public function moneyfund($value='')
    {
        $html = '';
        $html .= '<aside><div class="subNav">';
        $html .= '<a href="'.\yii\helpers\Url::to(['money-fund/index']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('money-fund/index') ? 'subCur' : '').'">交易记录</a>';
        // $html .= '<a href="'.\yii\helpers\Url::to(['money-fund/detail']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('money-fund/detail') ? 'subCur' : '').'">持仓详情</a>';
        $html .= '</div></aside>';

        return $html;
    }

    public function statistics($value='')
    {
        $html = '';
        $html .= '<aside><div class="subNav">';
        foreach (Yii::$app->admin->getSubmenus(6) as $key => $value) {
            $html .= '<a href="'.Url::to([$value['Route']]).'" class="subLink '.(Yii::$app->request->pathInfo == $value['Route'] ? 'subCur' : '').'">'.$value['Flag'].'</a>';
        }
        // $html .= '<a href="'.\yii\helpers\Url::to(['statistics/index']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('statistics/index') ? 'subCur' : '').'">统计数据</a>';
        // $html .= '<a href="'.\yii\helpers\Url::to(['statistics/position']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('statistics/position') ? 'subCur' : '').'">用户持仓</a>';
        $html .= '</div></aside>';

        return $html;
    }

    public function maintain($value='')
    {
        $html = '';
        $html .= '<aside><div class="subNav">';
        foreach (Yii::$app->admin->getSubmenus(7) as $key => $value) {
            if ($value['Route'] === 'business/category') {
                $this->curSubMenuList = ['business/category','business/add-cat','business/edit-cat'];
            }elseif ($value['Route'] === 'business/category-news') {
                $this->curSubMenuList = ['business/category-news','business/add-news-cat','business/news','business/add-news','business/edit-news'];
            }elseif ($value['Route'] === 'business/theme') {
                $this->curSubMenuList = ['business/theme','business/add-theme','business/edit-theme'];
            }elseif ($value['Route'] === 'business/portfolio') {
                $this->curSubMenuList = ['business/portfolio','business/add-portfolio','business/edit-portfolio','business/portfolio-fund-list'];
            }elseif ($value['Route'] === 'business/fund-info') {
                $this->curSubMenuList = ['business/fund-info','business/edit-fund-info'];
            }elseif ($value['Route'] === 'business/baccount') {
                $this->curSubMenuList = ['business/baccount','business/add-baccount','business/edit-baccount'];
            }elseif ($value['Route'] === 'business/trade-remit') {
                $this->curSubMenuList = ['business/trade-remit','business/upload-remit','business/ensure-remit','business/detail-remit'];
            }else{
                $this->curSubMenuList = [$value['Route']];
            }

            $html .= '<a href="'.Url::to([$value['Route']]).'" class="subLink '.(in_array(Yii::$app->request->pathInfo, $this->curSubMenuList) ? 'subCur' : '').'">'.$value['Flag'].'</a>';
        }

        /*$html .= '<a href="'.\yii\helpers\Url::to(['business/category']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/category','business/add-cat','business/edit-cat']) ? 'subCur' : '').'">基金类型</a>';
        if (Yii::$app->admin->isSuperAdmin) {
            $html .= '<a href="'.\yii\helpers\Url::to(['business/category-news']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/category-news','business/add-news-cat','business/news','business/add-news','business/edit-news']) ? 'subCur' : '').'">财经资讯</a>';
            // $html .= '<a href="'.\yii\helpers\Url::to(['business/theme']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/theme','business/add-theme','business/edit-theme']) ? 'subCur' : '').'">主题推荐</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/hot']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/hot') ? 'subCur' : '').'">热销基金</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/portfolio']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/portfolio','business/add-portfolio','business/edit-portfolio','business/portfolio-fund-list']) ? 'subCur' : '').'">组合列表</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/fund-info']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/fund-info','business/edit-fund-info']) ? 'subCur' : '').'">基金维护</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/cooperation']).'" class="subLink '.(\yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/cooperation') ? 'subCur' : '').'">商户合作</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/baccount']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/baccount','business/add-baccount','business/edit-baccount']) ? 'subCur' : '').'">企业账户</a>';
            $html .= '<a href="'.\yii\helpers\Url::to(['business/trade-remit']).'" class="subLink '.(in_array(\yii\helpers\Url::to(Yii::$app->request->pathInfo), ['business/trade-remit','business/upload-remit','business/ensure-remit','business/detail-remit']) ? 'subCur' : '').'">交易打款</a>';
        }*/
        $html .= '</div></aside>';
        
        return $html;
    }

    
}
