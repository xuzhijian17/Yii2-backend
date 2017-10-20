<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;

class ChangeCardLog extends ActiveRecord
{
	public static function tableName()
    {
        return 'change_bankcard_log';
    }
    
}
