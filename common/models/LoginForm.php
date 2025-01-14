<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username'], 'required','message' => 'ชื่อผู้ใช้งานต้องไม่ใช่ค่าว่าง.'],
            [['password'], 'required','message' => 'รหัสผ่านต้องไม่ใช่ค่าว่าง.'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    public function attributeLabels()
    {
        return [

            'username'=>'ชื่อผู้ใช้งาน',
            'password'=>'รหัสผ่าน',
            'rememberMe'=>'จำฉันไว้ในระบบ'

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
                $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
//        if ($this->validate()) {
//            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
//        } else {
//            return false;
//        }
        if (!$this->validate()) {
            return false;
        }

        if(Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)){
            if(Yii::$app->id=='app-backend' && !Yii::$app->user->can('Admin') &&!Yii::$app->user->can('Manager')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้ ...!');
            }
            return true;
        }
        return false;

    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
