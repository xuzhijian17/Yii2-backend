<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use common\lib\CommFun;
use common\lib\HundSun;

class Password extends Model
{
    public $hcid;
    public $newpwd;
    public $oldpwd;
    public $pwdtype;

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


    public function rules()
    {
        return [
            [['hcid', 'newpwd', 'oldpwd'], 'required'],
            [['newpwd'], 'compare', 'compareAttribute'=>'oldpwd', 'operator'=>'!='], //新密码不能和旧密码相同
            [['newpwd', 'oldpwd'], 'string', 'min'=>6, 'max'=>12], // 密码长度必须大于6小于32位
        ];
    }


    /**
     * modification user password logic.
     */
    public function setPassword()
    {
        if ($this->hasErrors()) {
            $this->handleCode('-201');
            // return CommFun::renderFormat('-201',[],['message'=>",请检查输入的密码是否符合规范"]);
        }

        $oHundSun = new HundSun($this->hcid);
        $priv = ['newpwd'=>$this->newpwd,'oldpwd'=>$this->oldpwd,'pwdtype'=>$this->pwdtype];
        $rs = $oHundSun->apiRequest('C012',$priv);
        
        if (isset($rs['code']) && $rs['code'] == 'ETS-5BP0000') {
            $this->syncPassword();   // 修改完成密码后，还需同步下本地库密码
        }

        return $rs;
    }

    /**
     * 同步密码到本地数据库
     */
    public function syncPassword($value='')
    {
        $user = (new Query)
            ->select(['Pass'])
            ->from('user')
            ->where(['id' => $this->hcid])
            ->one(self::getDb())
        ;
        // 用户是否存在
        if (!$user) {
            $this->handleCode('-7');
        }
        
        // Generate hash password.
        $newHashPwd = CommFun::AutoEncrypt($this->newpwd);
          
        $sql = "UPDATE user SET Pass=? WHERE id=?";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(1, $newHashPwd);
        $command->bindParam(2, $this->hcid);
        $affected_rows = $command->execute();

        if ($affected_rows === false) {
            Yii::error("本地库密码同步修改失败");
        }

        return $affected_rows;
    }
}
