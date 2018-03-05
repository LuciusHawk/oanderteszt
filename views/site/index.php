<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* //@var $searchModel app\models\MonitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Monitors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monitor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Monitor'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            'id',
            'entityName',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },

                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url);
                    }

                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = Url::to(['site/view', 'id' => $model->id]);
                        return $url;
                    }

                    if ($action === 'update') {
                        $url = Url::to(['site/update', 'id' => $model->id]);
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::to(['site/delete', 'id' => $model->id]);
                        return $url;
                    }

                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<style>
    .glyphicon {
        margin-right: 1rem;
    }
    td:first-child {
        width: 7rem;
    }
    td:last-child {
        text-align: right;
        width: 15rem;
    }
</style>
