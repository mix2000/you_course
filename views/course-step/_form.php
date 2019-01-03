<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CourseStep */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-step-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_course')->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\Course::find()->orderBy('name')->all(),'id','name')) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
