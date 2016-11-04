<?php
use bl\cms\shop\common\entities\PartnerRequest;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $partner PartnerRequest
 * @var $form ActiveForm
 */

?>

<div class="partner-request-send row">

    <div class="col-md-3">
        <?= $this->render('../user/settings/_menu') ?>
    </div>

    <div class="col-md-9">
        <?php if (!Yii::$app->user->can('productPartner')) : ?>
            <h1><?= Yii::t('shop', 'Partner request'); ?></h1>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => [
                    'partner-request/send',
                ],
                'options' => ['class' => 'tab-content']
            ]);
            ?>

            <?= $form->field($partner, 'company_name')->label(\Yii::t('shop', 'Company name')); ?>
            <?= $form->field($partner, 'website')->label(\Yii::t('shop', 'Website')); ?>
            <?= $form->field($partner, 'message')->textarea(['rows' => 7])->label(\Yii::t('shop', 'Message')); ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php else : ?>
            <h3><?= \Yii::t('shop', 'You are partner already.'); ?></h3>
        <?php endif; ?>
    </div>
</div>
