<?php
namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use backend\models\BaseModel;
use common\lib\CommFun;

/**
* Backend login model.
*/
class BELoginForm extends BaseModel
{
    public $instid;
	public $username;
    public $password;
    public $rememberMe = true;

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
    public static function getTable($instid='')
    {
        return 'admin'.$instid;
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
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
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean','on'=>'default'],
            // password is validated by validatePassword()
            ['password', 'validatePassword','on'=>'default'],
            ['instid', 'required','on'=>'register'],
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
            'default' => ['username','password','rememberMe'],
            'register' => ['instid','username','password'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) { 
            $data = (new Query)
                ->select(['*'])
                ->from(self::getTable())
                ->where(['Username'=>$this->username, 'Status'=>0])
                ->one(self::getDb())
            ;
            
            if (empty($data)) {
                $this->addError($attribute, 'Incorrect username.');
            }
            
            if (version_compare(PHP_VERSION,'5.5.0','<')) {
                $this->addError($attribute, 'Please update your PHP version to 5.5.0');
            }
            
            // The `password_verify()` function need to PHP version gt 5.5.0
            if (password_verify($this->password, $data['Password'])) {
                if (isset($data['id']) && !empty($data['id'])) {
                    Yii::$app->session->set(Yii::$app->admin->idParam,$data['id']);
                }
            }else{
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }

    /**
     * Set the password.
     * This method use password_hash() set password, so make sure you php version gt 5.5.0.
     *
     */
    public function setPassword()
    {
        $data = (new Query)
            ->from('admin')
            ->where(['Username'=>$this->username,'Instid'=>$this->instid,'Status'=>0])
            ->count('*',self::getDb())
        ;

        if (!empty($data)) {
            return CommFun::renderFormat('108');
        }

        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            return false;
        }
        
        $rs = self::getDb()->createCommand()->insert(self::getTable(), [
            'Instid' => $this->instid,
            'Username' => $this->username,
            'Password' => password_hash($this->password,PASSWORD_DEFAULT),    // The `password_hash()` function need to PHP version gt 5.5.0
        ])->execute();

        return $rs;
    }
}
