<?php

/* @var $this yii\web\View */
/* @var $form sky\yii\web\ActiveForm */

/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use sky\yii\web\ActiveForm;

$this->title = 'Login';
?>
<div class="site-login container">
    <div class="row">
        <div class="col-sm-1 col-md-3"></div>
        <div class="col-sm-10 col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <?= Html::encode($this->title) ?>
                </div>
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'id' => 'login-form',
                    'model' => $model,
                ]); ?>
                <div class="card-body">
                    <p>Please fill out the following fields to login:</p>


                    <?= $form->input('email')->textInput(['autofocus' => true]) ?>

                    <?= $form->input('password')->passwordInput() ?>

                    <?= $form->input('rememberMe')->checkbox([
                        //'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    ]) ?>

                </div>
                <div class="card-footer">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
