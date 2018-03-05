<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Monitor */
/* @var $attributes array */


$this->title = Yii::t('app', 'Update Monitor: {nameAttribute}', [
    'nameAttribute' => $model->entityName,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Monitors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entityName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="monitor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>

</div>
