<?php
namespace sky\yii\assets;

class DropzoneAsset extends \yii\web\AssetBundle
{
    public $baseUrl = 'https://unpkg.com/dropzone@5';

    public $js = [
        'dist/min/dropzone.min.js',
    ];

    public $css = [
        'dist/min/dropzone.min.css'
    ];

    public $depends = [
        YiiAsset::class,
    ];
}