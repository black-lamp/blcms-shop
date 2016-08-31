<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel bl\cms\shop\common\entities\SearchPartnerRequest */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('partner', 'Partner Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*Sender*/
            [
                'headerOptions' => ['class' => 'text-center'],
                'value' => 'sender.username',
                'label' => Yii::t('shop', 'User'),
                'format' => 'text',
                'contentOptions' => ['class' => 'project-title'],
            ],
            [
                'headerOptions' => ['class' => 'text-center'],
                'value' => 'company_name',
                'label' => Yii::t('shop', 'Company name'),
                'format' => 'text',
                'contentOptions' => ['class' => 'project-title'],
            ],
            [
                'headerOptions' => ['class' => 'text-center'],
                'value' => 'website',
                'label' => Yii::t('shop', 'Website'),
                'format' => 'text',
                'contentOptions' => ['class' => 'project-title'],
            ],
            [
                'headerOptions' => ['class' => 'text-center'],
                'value' => 'created_at',
                'label' => Yii::t('shop', 'Created'),
                'format' => 'text',
                'contentOptions' => ['class' => 'project-title'],
            ],

            [
                'label' => 'Ссылка',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a(
                        'Перейти',
                        Url::toRoute(['view', 'id' => $data->id]),
                        [
                            'class' => 'btn btn-primary btn-xs'
                        ]
                    );
                }
            ],

            /*Disable action column*/
            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => false
            ],
        ],
    ]); ?>
</div>
