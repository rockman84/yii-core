<?php
namespace sky\yii\web;

class View extends \yii\web\View
{
    public $bodyHeader = null;
    
    public $viewBodyHeader = null;
    
    public function renderBodyHeader()
    {
        if ($this->bodyHeader === null) {
            $this->setBodyHeader();
        }
        return $this->bodyHeader;
    }
    
    public function setBodyHeader($view = null, $params = [], $context = null)
    {
        $view = $view ? : $this->viewBodyHeader;
        $this->bodyHeader = $view ? $this->render($view, $params, $context) : '';
    }
}