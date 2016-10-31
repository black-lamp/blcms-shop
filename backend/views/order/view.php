<?php
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\Order
 * @var $statuses[] bl\cms\cart\models\OrderStatus
 */

?>
<div class="panel panel-default">

    <div class="panel-heading">
        <h1><?= \Yii::t('shop', 'Order #') . $model->id . ':'; ?></h1>
    </div>

    <!--CHANGE STATUS-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Order status'); ?>
        </h2>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'status')->dropDownList(ArrayHelper::map($statuses, 'id', function($model) {
            return $model->translation->title;
        }), ['options' => [$model->status => ['selected' => true]]]); ?>
        <?= Html::submitButton(Yii::t('shop', 'Change status'), ['class' => 'btn btn-primary']); ?>
        <?= Html::a(Yii::t('shop', 'Close'), Url::toRoute('/shop/order'), ['class' => 'btn btn-danger']) ?>
        <?php $form::end(); ?>
    </div>

    <!--ORDER DETAILS-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Order details'); ?>
        </h2>

        <p><b><?= \Yii::t('shop', 'Customer name'); ?>:</b> <?=$model->user->profile->name; ?></p>
        <p><b><?= \Yii::t('shop', 'Surname'); ?>:</b> <?=$model->user->profile->surname; ?></p>
        <p><b><?= \Yii::t('shop', 'Patronymic'); ?>:</b> <?=$model->user->profile->patronymic; ?></p>
        <p><b><?= \Yii::t('shop', 'Phone number'); ?>:</b> <?=$model->user->profile->phone; ?></p>


    </div>

    <!--PRODUCT LIST-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Product list'); ?>
        </h2>
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
                            Yii::$app->urlManager->hostInfo . '/shop/' . $model->product->category->translation->seoUrl . '/' . $model->product->translation->seoUrl);
                    }
                ],
                [
                    'label' => 'count',
                    'format' => 'raw',
                    'value' => 'count'
                ],
                [
                    'label' => 'price',
                    'value' => function($model) {
                        $price = $model->price;
                        return $price;
                    }
                ],
                /*ACTIONS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => \Yii::t('shop', 'Delete'),

                    'value' => function ($model) {

                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete-product', 'id' => $model->id]),
                            ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right pjax']);

                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-2 text-center'],
                ],
            ],
        ]); ?>
    </div>
</div>