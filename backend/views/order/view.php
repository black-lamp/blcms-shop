<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\Order
 */

?>
<div class="order-view">

    <p>
        <?= Html::a(Yii::t('shop', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('shop', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('shop', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'last_name',
            'email:email',
            'phone',
            'address',
            'status',
        ],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->product->translation->title,
                        Url::to(['/shop/' . $model->product->category->translation->seoUrl . '/' . $model->product->translation->seoUrl]));
                }
            ],
            'count',
            /*ACTIONS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'attribute' => \Yii::t('shop', 'Delete'),

                'value' => function ($model) {

                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['remove', 'id' => $model->id]),
                        ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right pjax']);

                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-md-2 text-center'],
            ],
        ],
    ]); ?>

</div>