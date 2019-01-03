<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CourseStep */

$this->title = 'Update Course Step: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Course Steps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id_step]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="course-step-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
