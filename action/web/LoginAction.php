<?php
namespace sky\yii\action\web;

use sky\yii\web\Controller;
use yii\base\Action;
use Yii;
use yii\base\InvalidConfigException;

class LoginAction extends Action
{
    /**
     * @var string
     */
    public $view = '@sky/yii/views/action/login';

    /**
     * @var string
     */
    public $formModelClass = 'sky\yii\models\form\Login';

    /**
     * @var string|null
     */
    public $successCallback = null;

    /**
     * @var string|null
     */
    public $failCallback = null;

    public function run()
    {
        if (!$this->controller instanceof Controller) {
            throw new InvalidConfigException("Controller must instance of sky\yii\web\Controller");
        }
        if (!Yii::$app->user->isGuest) {
            return $this->controller->goHome();
        }

        $model = Yii::createObject($this->formModelClass);

        if ($this->controller->isAjaxValidation($model)) {
            return $this->controller->jsonValidateModel($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if ($this->successCallback) {
                return $this->controller->{$this->successCallback}($model);
            }
            return $this->controller->goBack();
        } elseif ($this->failCallback) {
            return $this->controller->{$this->successCallback}($model);
        }

        $model->password = '';

        return $this->controller->render($this->view, [
            'model' => $model,
        ]);

        $this->controller->render($this->view);
    }
}