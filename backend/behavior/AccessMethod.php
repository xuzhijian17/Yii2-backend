<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\behavior;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;
use yii\web\User;
use yii\web\Request;
use yii\web\Response;
use yii\helpers\Json;
use common\lib\CommFun;
use yii\helpers\Url;

/**
 * AuthMethod is a base class implementing the [[AuthInterface]] interface.
 * @author Xuzhijian17
 */
class AccessMethod extends ActionFilter
{
    public $superAdminAction = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Access judgement
    	if (Yii::$app->admin->getIsGuest() && !in_array($action->id, $this->except)) {
            Url::remember(Url::canonical(),Yii::$app->admin->returnUrlParam);   // Remember current route url be used for login success callback.
            return !Yii::$app->response->redirect(Yii::$app->admin->loginUrl);
        }

        // Just super admin can access action
		if (!Yii::$app->admin->getIsSuperAdmin() && in_array($action->id, $this->superAdminAction)) {
            echo json_encode(CommFun::renderFormat('100'), JSON_UNESCAPED_UNICODE);
            return false;
        }
        
        // If the partner is remove(`partner` table and `Status` field is -1).
        if (Yii::$app->admin->getInstid() === null) {
            echo json_encode(CommFun::renderFormat('105'), JSON_UNESCAPED_UNICODE);
            return false;
        }
		
		return true;
    }
}
