<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\db\Query;

/**
 * User is the class for the "user" application component that manages the user authentication status.
 *
 * You may use [[isGuest]] to determine whether the current user is a guest or not.
 * If the user is a guest, the [[identity]] property would return null. Otherwise, it would
 * be an instance of [[IdentityInterface]].
 *
 * You may call various methods to change the user authentication status:
 *
 * - [[login()]]: sets the specified identity and remembers the authentication status in session and cookie.
 * - [[logout()]]: marks the user as a guest and clears the relevant information from session and cookie.
 * - [[setIdentity()]]: changes the user identity without touching session or cookie.
 *   This is best used in stateless RESTful API implementation.
 *
 * Note that User only maintains the user authentication status. It does NOT handle how to authenticate
 * a user. The logic of how to authenticate a user should be done in the class implementing [[IdentityInterface]].
 * You are also required to set [[identityClass]] with the name of this class.
 *
 * User is configured as an application component in [[\yii\web\Application]] by default.
 * You can access that instance via `Yii::$app->user`.
 *
 * You can modify its configuration by adding an array to your application config under `components`
 * as it is shown in the following example:
 *
 * ```php
 * 'user' => [
 *     'identityClass' => 'app\models\User', // User must implement the IdentityInterface
 *     'enableAutoLogin' => true,
 *     // 'loginUrl' => ['user/login'],
 *     // ...
 * ]
 * ```
 *
 * @property string|integer $id The unique identifier for the user. If null, it means the user is a guest.
 * This property is read-only.
 * @property IdentityInterface|null $identity The identity object associated with the currently logged-in
 * user. `null` is returned if the user is not logged in (not authenticated).
 * @property boolean $isGuest Whether the current user is a guest. This property is read-only.
 * @property string $returnUrl The URL that the user should be redirected to after login. Note that the type
 * of this property differs in getter and setter. See [[getReturnUrl()]] and [[setReturnUrl()]] for details.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Admin extends Component
{
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    const EVENT_AFTER_LOGIN = 'afterLogin';
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';
    const EVENT_AFTER_LOGOUT = 'afterLogout';

    /**
     * @var boolean whether to enable cookie-based login. Defaults to false.
     * Note that this property will be ignored if [[enableSession]] is false.
     */
    public $enableAutoLogin = false;
    /**
     * @var boolean whether to use session to persist authentication status across multiple requests.
     * You set this property to be false if your application is stateless, which is often the case
     * for RESTful APIs.
     */
    public $enableSession = true;
    /**
     * @var string|array the URL for login when [[loginRequired()]] is called.
     * If an array is given, [[UrlManager::createUrl()]] will be called to create the corresponding URL.
     * The first element of the array should be the route to the login action, and the rest of
     * the name-value pairs are GET parameters used to construct the login URL. For example,
     *
     * ```php
     * ['site/login', 'ref' => 1]
     * ```
     *
     * If this property is null, a 403 HTTP exception will be raised when [[loginRequired()]] is called.
     */
    public $loginUrl = ['site/login'];
    /**
     * @var string the session variable name used to store the value of [[id]].
     */
    public $idParam = '_id';
    /**
     * @var string the session variable name used to store the value of [[returnUrl]].
     */
    public $returnUrlParam = '__returnUrl';


    private $_instid;
    private $_isGuest;
    private $_isSuperAdmin;
    private $_menus = [];
    private $_admins = [];
    private $_instName;
    private $_instFullName;
    private $_instPwd;
    private $_instDivide;
    private $_instStatus;
    private $_instInfo;
    private $_isSecretKeySet;
    private $_subMenu;
    private $_optionMenu;

    /**
     * Initializes the application component.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Get instid
     * @return instid|null
     */
    public function getInstid($value='')
    {
        if ($this->_instid !== null) {
            return $this->_instid;
        }

        $id = Yii::$app->session->get($this->idParam);

        $sql = "SELECT p.Instid FROM admin a LEFT JOIN partner p ON a.Instid=p.Instid AND p.`Status`=0 WHERE a.`Status`=0 AND a.id=:id";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(':id', $id);
        
        return $command->queryScalar();
    }

    /**
     * Is guest user
     * @return boolean
     */
    public function getIsGuest($value='')
    {
        return Yii::$app->session->get($this->idParam) === null;
    }

    /**
     * Is super admin user
     * @return boolean
     */
    public function getIsSuperAdmin($instid='')
    {
        if ($this->getIsGuest()) {
            return false;
        }

        if ($this->_isSuperAdmin) {
            return $this->_isSuperAdmin;
        }

        if ($instid === '') {
            $instid = $this->getInstid();
        }

        return $this->_isSuperAdmin = (new Query)->select("SuperAdmin")->from("admin")->where(["Instid"=>$instid])->scalar() ? true : false;
    }

    /**
     * Get menus
     * @return array|false
     */
    public function getMenus($value='')
    {
        if ($this->getIsGuest()) {
            return false;
        }

        if (!empty($this->_menus)) {
            return $this->_menus;
        }

        $id = Yii::$app->session->get($this->idParam);

        $sql = "SELECT * FROM bus_permission bp LEFT JOIN perm_list pl ON bp.PermId=pl.id AND pl.Status=0 AND pl.ParentId=0 WHERE bp.Status=0 AND bp.AdminId=".$id." ORDER BY pl.Order";
        $command = Yii::$app->db->createCommand($sql);
        return $this->_menus = $command->queryAll();
    }

    /**
     * Get submenu permission
     * @return true|false
     */
    public function getSubmenus($permid,$id='')
    {
        if ($this->getIsGuest()) {
            return false;
        }
        
        if ($id === '') {
            $id = Yii::$app->session->get($this->idParam);
        }

        if ($this->_subMenu !== null) {
            return $this->_subMenu;
        }

        $sql = "SELECT * FROM bus_permission bp LEFT JOIN perm_list pl ON bp.PermId=pl.id AND pl.Status=0 WHERE bp.Status=0 AND bp.AdminId=".$id." AND pl.ParentId=".$permid." ORDER BY pl.Order";
        $command = Yii::$app->db->createCommand($sql);
        return $this->_subMenu = $command->queryAll();
    }

    /**
     * Get inst list
     * @return array|false
     */
    public function getInstList($instid='')
    {
        if ($this->getIsGuest()) {
            return false;
        }

        if (!empty($this->_admins)) {
            return $this->_admins;
        }

        if ($this->getIsSuperAdmin($instid)) {
            $this->_admins = (new Query)->from('partner')->all();
        }else{
            $this->_admins = (new Query)->from('partner')->where(['Instid'=>$this->getInstid(),'Status'=>0])->all();
        }

        return $this->_admins ?: [];
    }

    /**
     * Get partner info
     * @return array|false
     */
    public function getInstInfo($instid='')
    {
        if ($this->getIsGuest()) {
            return false;
        }

        if ($instid === '') {
            $instid = $this->getInstid();
        }

        if ($this->_instInfo !== null) {
            return $this->_instInfo;
        }

        return $this->_instInfo = (new Query)->from('partner')->where(['Instid'=>$instid])->one();
    }

    /**
     * Is SecretKey set
     * @return true|false
     */
    public function getIsSecretKeySet($id='')
    {
        if ($this->getIsGuest()) {
            return false;
        }
        
        if ($id === '') {
            $id = Yii::$app->session->get($this->idParam);
        }

        if ($this->_isSecretKeySet !== null) {
            return $this->_isSecretKeySet;
        }

        return $this->_isSecretKeySet = (new Query)->from('secretKey_permission')->where(['AdminId'=>$id])->scalar() ? true : false;
    }

    /**
     * Get Option Menu
     * @return true|false
     */
    public function getOptionMenu($id='')
    {
        if ($this->getIsGuest()) {
            return false;
        }
        
        if ($id === '') {
            $id = Yii::$app->session->get($this->idParam);
        }

        if ($this->_optionMenu !== null) {
            return $this->_optionMenu;
        }

        return $this->_optionMenu = (new Query)->from('option_menu')->where(['AdminId'=>$id,'Status'=>0])->orderBy('Order')->all();
    }
}
