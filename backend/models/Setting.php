<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Setting model.
*/
class Setting extends BaseModel
{
    public $Instid;
    public $Divide;
    public $Status;
    public $PassWord;

    public $oldPwd;
    public $newPwd1;
    public $newPwd2;

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns the database connection used by this Model class.
     * By default, the "db" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     * @param [string] db component name.
     * @return Connection the database connection used by this Model class.
     */
    public static function getDb($db_name='')
    {
        return $db_name ? Yii::$app->$db_name : Yii::$app->db;
    }

    /**
     * Returns the trade order table name.
     * @param [mixed] instid.
     * @return string|false.
     */
    public static function getTable()
    {
        return 'partner';
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [
            // All scenario validate rule.
            ['Instid','required'],
            // updateSecretKey scenario validate rule.
            // ['PassWord','compare', 'compareValue' => 50, 'operator' => '>','on'=>'updateSecretKey'],
            ['PassWord', 'string', 'max'=>50], 
            // modifyPwd scenario validate rule.
            [['oldPwd','newPwd1','newPwd2'], 'required','on'=>'modifyPwd'],
            [['oldPwd','newPwd1','newPwd2'], 'string', 'length'=>[3,50],'on'=>'modifyPwd'], 
            [['newPwd1','newPwd2'], 'compare', 'compareAttribute'=>'oldPwd', 'operator'=>'!=','on'=>'modifyPwd'],
            [['newPwd2'], 'compare', 'compareAttribute'=>'newPwd1', 'operator'=>'==','on'=>'modifyPwd'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios($value='')
    {
        return [
            'default' => ['Instid'],
            'updateSecretKey' => ['Instid','Divide','Status','PassWord'],
            'modifyPwd' => ['oldPwd','newPwd1','newPwd2'],
        ];
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        if ($attribute == 'PassWord') {
            $this->_errors = CommFun::renderFormat('301',[],$error);
        }elseif(in_array($attribute, ['oldPwd','newPwd1','newPwd2'])){
            $this->_errors = CommFun::renderFormat('109',[],$error);
        }else{
            $this->_errors[$attribute][] = $error;
        }
    }

    public function getPartner($value='')
    {        
        return (new Query)->from(self::getTable())->where(['Instid'=>$this->Instid])->one();
    }
    
    /**
     * 更新商户秘钥
     */
    public function updateSecretKey()
    {
        $condition = ['PassWord'=>$this->PassWord];

        if (!$this->Instid) {
            $this->Instid = Yii::$app->admin->instid;
        }

        if ($this->Status) {
            $condition['Status'] = $this->Status;
        }

        if ($this->Divide) {
            $condition['Divide'] = $this->Divide;
        }
        
        // UPDATE
        $command = self::getDb()->createCommand();
        $rs = $command->update(self::getTable(), $condition ,['Instid' => $this->Instid])->execute();
        
        if ($rs) {
            Yii::$app->redis->executeCommand('HDEL',['partner_info', $this->Instid]);   // Delete redis cache
        }

        return $rs;
    }

    /**
     * Modify the password.
     * This method use password_hash() set password, so make sure you php version gt 5.5.0.
     *
     */
    public function modifyPassword()
    {
        $id = Yii::$app->session->get(Yii::$app->admin->idParam);

        $data = (new Query)
            ->select(['*'])
            ->from('admin')
            ->where(['id'=>$id, 'Status'=>0])
            ->one(self::getDb())
        ;

        if (empty($data)) {
            return CommFun::renderFormat('106');
        }

        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            return false;
        }

        if (!password_verify($this->oldPwd, $data['Password'])) {
            return CommFun::renderFormat('109',[],['rewrite'=>false,'message'=>'（原密码）']);
        }
        
        $rs = self::getDb()->createCommand()->update('admin', [
            'Password' => password_hash($this->newPwd2,PASSWORD_DEFAULT),    // The `password_hash()` function need to PHP version gt 5.5.0
        ],['id' => $id])->execute();

        return $rs;
    }
}
