<?php

use bl\cms\shop\common\entities\PartnerRequest;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model bl\cms\shop\common\entities\PartnerRequest */

$this->title = $model->company_name;
?>
<div class="partner-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--MODERATION-->
    <?php if (Yii::$app->user->can('moderatePartnerRequest') && $model->moderation_status == PartnerRequest::STATUS_ON_MODERATION) : ?>
    <div class="panel-body">
        <h2><?= \Yii::t('shop', 'Moderation');?></h2>
        <p><?= \Yii::t('shop', 'This company status is "on moderation". You may accept or decline it.'); ?></p>
        <?= Html::a(\Yii::t('shop', 'Accept'), Url::toRoute(['change-partner-status', 'id' => $model->id, 'status' => PartnerRequest::STATUS_SUCCESS]), ['class' => 'btn btn-primary btn-xs']); ?>
        <?= Html::a(\Yii::t('shop', 'Decline'), Url::toRoute(['change-partner-status', 'id' => $model->id, 'status' => PartnerRequest::STATUS_DECLINED]), ['class' => 'btn btn-danger btn-xs']); ?>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sender_id',
            'company_name',
            'website',
            'message:ntext',
            'created_at',
            'moderation_status',
            'moderated_by',
            'moderated_at',
        ],
    ]) ?>

</div>
