<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\cart\models\SearchOrder
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use bl\cms\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= \Yii::t('shop', 'Order list'); ?>
        </h5>
    </div>

    <?= GridView::widget([
        'filterRowOptions' => ['class' => ''],
        'options' => [
            'class' => 'project-list'
        ],
        'tableOptions' => [
            'id' => 'my-grid',
            'class' => 'table table-hover'
        ],

        'summary' => "",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*Customer*/
            [
                'headerOptions' => ['class' => 'text-center col-md-3'],
                'value' => function ($model) {

                    $customer = null;
                    if (!empty($model->user->profile->name) || !empty($model->user->profile->surname)) {
                        $customer = Html::a(
                            $model->user->profile->name . ' ' . $model->user->profile->surname,
                            Url::toRoute(['view', 'id' => $model->id])
                        );
                    }
                    return $customer;
                },
                'label' => Yii::t('shop', 'Customer'),
                'format' => 'html',
                'contentOptions' => ['class' => 'text-center project-title col-md-3'],
            ],

            [
                'headerOptions' => ['class' => 'text-center col-md-3'],
                'attribute' => 'status',
                'filter' => ArrayHelper::map(OrderStatus::find()->all(), 'id', 'title'),

                'value' => function ($model) {
                    $status = (!empty($model->orderStatus->title)) ? $model->orderStatus->title : '';
                    return Html::a($status, Url::toRoute(['view', 'id' => $model->id]),
                        ['class' => 'btn btn-default btn-xs']);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center col-md-3 text-center'],
            ],

            /*ACTIONS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'attribute' => \Yii::t('shop', 'Manage'),

                'value' => function ($model) {
                    return Html::a('<span class="glyphicon glyphicon-star"></span>', Url::toRoute(['view', 'id' => $model->id]),
                        ['title' => Yii::t('shop', 'Status and details'), 'class' => 'btn btn-primary pjax m-r-md']) .
                    Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete', 'id' => $model->id]),
                        ['title' => Yii::t('shop', 'Delete'), 'data-method' => 'post', 'class' => 'btn btn-danger pjax']);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center col-md-2 text-center'],
            ]
        ],
    ]); ?>
</div>