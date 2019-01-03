<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Course Steps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-step-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Course Step', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_step',
            'id_course',
            'name',
            'sort',
            'description',
            //'video',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
