<?php
namespace sky\yii\models\form;

use sky\yii\models\User;
use Yii;

class Signup extends \yii\base\Model
{

    public $email;
    
    public $password;

    public $confirmPassword;
    
    public function rules() {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            [['confirmPassword', 'password'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],
            ['email', 'trim'],
            [['email'], 'checkEmail'],
            [['email'], 'filter', 'filter' => 'strtolower'],
        ];
    }
    
    public function checkEmail($attribute, $params)
    {
        $user = User::findOne(['email' => $this->{$attribute}]);
        if ($user) {
            return $this->addError($attribute, 'Email sudah terdaftar.');
        }
    }
    
    public function save()
    {
        $userClass = Yii::$app->user->identityClass;
        if ($this->validate()) {
            $user = Yii::createObject($userClass);
            $user->load($this->getAttributes(), '');
            $user->setPassword($this->password);
            if ($user->save()) {
                Yii::$app->user->login($user);
                return $user;
            }
        }
        return false;
    }
}