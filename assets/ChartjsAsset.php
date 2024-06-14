<?php
namespace sky\yii\assets;

use yii\web\YiiAsset;

/**
 * @see https://cdnjs.com/libraries/Chart.js
 */
class ChartjsAsset extends \yii\web\AssetBundle
{
    public $baseUrl = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1';

    public $js = [
//        'chart.min.js',
        'chart.umd.min.js',
//        'helpers.min.js',
    ];

    public $depends = [
        YiiAsset::class,
    ];
}