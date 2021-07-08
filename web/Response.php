<?php
namespace common\components;

use yii\helpers\Url;
use Yii;

class Response extends \yii\web\Response
{

    public function init()
    {
        parent::init();
        if (getenv('XXSS_PROTECTION_REPORT')) {
            $param = 'report=' . Yii::$app->urlManager->createAbsoluteUrl(['report']) . ';';
        } else {
            $param = 'mode=block;';
        }
        $this->headers->add('X-XSS-Protection', "1; {$param}");
    }

    public function addHeaderSecurity()
    {
        return [
            'Content-Security-Policy' => 'frame-src none;',
            'X-XSS-Protection' => 'value=1; mode=block;',
        ];
    }

}