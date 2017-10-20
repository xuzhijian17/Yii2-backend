<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * User model
 *
 */
class User extends Model implements IdentityInterface
{
    private $_uid;
    private $_auth_key;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        // $class_name = get_called_class();
        $class_name = new self;
        return new $class_name;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        $this->_uid = Yii::$app->session['user_login']['id'];
        return $this->_uid;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        $this->_auth_key = Yii::$app->security->generateRandomString();
        return $this->_auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->_auth_key === $authKey;
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($merid, $username, $password)
    {
        // sql
        $password_hash = password_hash($password);  // 数据库的密码，一串Hash值
        return password_verify($password, $password_hash);

        // return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

}
