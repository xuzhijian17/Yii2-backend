<?php
namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\CommFun;
use common\lib\HundSun;

class Password extends Model
{
	public $oldPassword;
	public $newPassword;
	public $repeatPassword;

    public $uid;

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
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Each rule is an array with the following structure:
     *
     * ```php
     * [
     *     ['attribute1', 'attribute2'],
     *     'validator type',
     *     'on' => ['scenario1', 'scenario2'],
     *     //...other parameters...
     * ]
     * ```
     *
     * where
     *
     *  - attribute list: required, specifies the attributes array to be validated, for single attribute you can pass a string;
     *  - validator type: required, specifies the validator to be used. It can be a built-in validator name,
     *    a method name of the model class, an anonymous function, or a validator class name.
     *  - on: optional, specifies the [[scenario|scenarios]] array in which the validation
     *    rule can be applied. If this option is not set, the rule will apply to all scenarios.
     *  - additional name-value pairs can be specified to initialize the corresponding validator properties.
     *    Please refer to individual validator class API for possible properties.
     *
     * A validator can be either an object of a class extending [[Validator]], or a model class method
     * (called *inline validator*) that has the following signature:
     *
     * ```php
     * // $params refers to validation parameters given in the rule
     * function validatorName($attribute, $params)
     * ```
     *
     * In the above `$attribute` refers to the attribute currently being validated while `$params` contains an array of
     * validator configuration options such as `max` in case of `string` validator. The value of the attribute currently being validated
     * can be accessed as `$this->$attribute`. Note the `$` before `attribute`; this is taking the value of the variable
     * `$attribute` and using it as the name of the property to access.
     *
     * Yii also provides a set of [[Validator::builtInValidators|built-in validators]].
     * Each one has an alias name which can be used when specifying a validation rule.
     *
     * Below are some examples:
     *
     * ```php
     * [
     *     // built-in "required" validator
     *     [['username', 'password'], 'required'],
     *     // built-in "string" validator customized with "min" and "max" properties
     *     ['username', 'string', 'min' => 3, 'max' => 12],
     *     // built-in "compare" validator that is used in "register" scenario only
     *     ['password', 'compare', 'compareAttribute' => 'password2', 'on' => 'register'],
     *     // an inline validator defined via the "authenticate()" method in the model class
     *     ['password', 'authenticate', 'on' => 'login'],
     *     // a validator of class "DateRangeValidator"
     *     ['dateRange', 'DateRangeValidator'],
     * ];
     * ```
     *
     * Note, in order to inherit rules defined in the parent class, a child class needs to
     * merge the parent rules with child rules using functions such as `array_merge()`.
     *
     * @return array validation rules
     * @see scenarios()
     */
	public function rules()
	{
	    return [
            /**
            * 使用required核心验证器,应用于所有场景或者使用'on'=>'default'
            */
            [['oldPassword'], 'required'],
            /**
            * 使用required核心验证器,应用于modifyPassword场景
            */
            [['oldPassword','newPassword','repeatPassword'], 'required', 'on'=>'modifyPassword'],   // 定义必填属性
            /**
            * 使用了compare核心验证器，只应用于modifyPassword场景
            */
            ['newPassword', 'compare', 'compareAttribute'=>'repeatPassword', 'on'=>'modifyPassword'],   // 2次密码不能重复
            [['newPassword','repeatPassword'], 'compare', 'compareAttribute'=>'oldPassword', 'operator'=>'!=', 'on'=>'modifyPassword'], //新密码不能和旧密码相同
	        [['oldPassword', 'newPassword', 'repeatPassword'], 'string', 'min'=>3, 'max'=>32, 'on'=>'modifyPassword'], // 密码长度必须大于6小于32位
            /**
            * 使用行内自定义验证器
            */
	        /*[['oldPassword'], function ($attribute, $params) {
                // 使用行内自定义验证器
                
                var_dump($params,$this->oldPassword);
            },'on'=>['modifyPasswords']],*/
            // [['oldPassword'], 'validatePassword','on'=>['modifyPassword']]
	    ];
	}

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * The returned array should be in the following format:
     *
     * ```php
     * [
     *     'scenario1' => ['attribute11', 'attribute12', ...],
     *     'scenario2' => ['attribute21', 'attribute22', ...],
     *     ...
     * ]
     * ```
     *
     * By default, an active attribute is considered safe and can be massively assigned.
     * If an attribute should NOT be massively assigned (thus considered unsafe),
     * please prefix the attribute with an exclamation character (e.g. `'!rank'`).
     *
     * The default implementation of this method will return all scenarios found in the [[rules()]]
     * declaration. A special scenario named [[SCENARIO_DEFAULT]] will contain all attributes
     * found in the [[rules()]]. Each scenario will be associated with the attributes that
     * are being validated by the validation rules that apply to the scenario.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
	public function scenarios()
	{
	    return [
            // 因为这里的scenarios方法并没有继承而是直接覆盖了原scenarios方法，所以需重新设置全局场景default。
            'default' => ['oldPassword'],
            // modifyPassword场景只针对'oldPassword', 'newPassword', 'repeatPassword' 3个属性进行规则验证
	        'modifyPassword' => ['oldPassword', 'newPassword', 'repeatPassword']
	    ];
	}

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
	/*public function validatePassword()
    {
        if (!$this->hasErrors()) {
            
            $sql = "SELECT Pass FROM user WHERE id=:uid";
            $command = self::getDb()->createCommand($sql);
            $command->bindParam(':uid', $this->uid);
            $pass = $command->queryScalar();

            if (!$pass) {
                $this->addError($attribute, "Incorrect uid `{$this->uid}`.");
            }

            // Verify Password
            if($this->oldPassword !== CommFun::AutoEncrypt($pass,'D')) {
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }*/


    /**
     * modification user password logic.
     */
	public function setPassword($uid,$pwdtype='t')
	{
        $user = (new Query)
            ->select(['Pass'])
            ->from('user')
            ->where(['id' => $uid])
            ->one(self::getDb())
        ;

        if (!$user) {
            return CommFun::renderFormat('-102');
        }

    	// Verify Password
	    if($this->oldPassword != CommFun::AutoEncrypt($user['Pass'],'D')) {
            return CommFun::renderFormat('-103');
        }
        
        $oHundSun = new HundSun($uid);

        $priv = ['newpwd'=>$this->newPassword,'oldpwd'=>$this->oldPassword,'pwdtype'=>$pwdtype];
        $rs = $oHundSun->apiRequest('C012',$priv);

        if (!isset($rs['code']) || $rs['code'] !== 'ETS-5BP0000') {
            return CommFun::renderFormat('-1000');
        }

        // Generate hash password.
        $newHashPwd = CommFun::AutoEncrypt($this->newPassword);
       
        $sql = "UPDATE user SET Pass=? WHERE id=?";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(1, $newHashPwd);
        $command->bindParam(2, $uid);
        $affected_rows = $command->execute();

        if ($affected_rows === false) {
        	Yii::error("Set password error.");
        	return CommFun::renderFormat('-1');
		}

        return  CommFun::renderFormat(0, $affected_rows ? ['redict_url'=>\yii\helpers\Url::to(Yii::$app->user->loginUrl)] : $affected_rows);
	}
}
