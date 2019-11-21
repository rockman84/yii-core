<?php
namespace sky\yii\web;

use Yii;
use app\components\form\ActiveForm;
use yii\web\Response;
use yii\base\Model;

/**
 * @property \yii\web\Application $app
 */
class Controller extends \yii\web\Controller
{       
    
    protected function jsonValidateModel(Model $model)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    
    protected function isAjaxValidation(Model $model, $formName = null)
    {
        return Yii::$app->request->isAjax && $model->load(Yii::$app->request->post(), $formName);
    }
    
    public function renderPjax($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        } else {
            return $this->render($view, $params);
        }
    }
    
    protected function renderSubLayout(Model $model, $layout, $view, $params = [])
    {
        $params = array_merge(['model' => $model], $params);
        return $this->render($layout, [
            'content' => $this->renderPartial($view, $params),
            'model' => $model,
        ]);
    }
    
    public function getApp()
    {
        return Yii::$app;
    }
}
