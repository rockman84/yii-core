<?php
namespace sky\yii\action\web;

use sky\yii\web\Controller;
use yii\base\Action;
use Yii;
use yii\base\InvalidConfigException;

class LogoutAction extends Action
{
    /**
     * @var string|null
     */
    public $successCallback = null;

    /**
     * @return \yii\web\Response
     * @throws InvalidConfigException
     */
    public function run()
    {
        if (!$this->controller instanceof Controller) {
            throw new InvalidConfigException("Controller must instance of sky\yii\web\Controller");
        }

        Yii::$app->user->logout();

        if ($this->successCallback) {
            return $this->controller->{$this->successCallback}($model);
        }

        return $this->controller->goHome();
    }
}