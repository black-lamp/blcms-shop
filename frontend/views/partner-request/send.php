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
 * @var $this ->context->staticPage \bl\cms\seo\common\entities\StaticPage
 */

?>

<div class="partner-request-send row">

    <h1 class="text-center"><?= (!empty($this->context->staticPage->translation->title)) ?
            $this->context->staticPage->translation->title : Yii::t('shop', 'Partner request'); ?>
    </h1>

    <?php if (!empty($this->context->staticPage->translation->text)) : ?>
            <div class="col-md-12">
                <?= $this->context->staticPage->translation->text; ?>
            </div>
    <?php endif; ?>

    <div class="col-md-12">
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->getPartnerStatus()) : ?>
            <h3><?= \Yii::t('shop', 'You have already sent a request.'); ?></h3>
        <?php else : ?>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => [
                    'partner-request/send',
                ],
                'options' => ['class' => 'tab-content']
            ]);
            ?>

            <?php if (Yii::$app->user->isGuest) : ?>
                <!--REGISTRATION-->
                <h2>
                    <?= Yii::t('shop', 'Registration'); ?>
                </h2>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($profile, 'name')->textInput()->label(\Yii::t('shop', 'Name')); ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($profile, 'patronymic')->textInput()->label(\Yii::t('shop', 'Patronymic')); ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($profile, 'surname')->textInput()->label(\Yii::t('shop', 'Surname')); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($user, 'email')->label(\Yii::t('shop', 'E-mail')) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($user, 'username')->label(\Yii::t('shop', 'Login')) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($user, 'password')->passwordInput()->label(\Yii::t('shop', 'Password')) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($profile, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                            'mask' => '(999)-999-99-99',
                        ])->label(\Yii::t('shop', 'Phone number')); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!--COMPANY INFO-->
            <h2>
                <?= Yii::t('shop', 'Company info'); ?>
            </h2>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($partner, 'company_name')->label(\Yii::t('shop', 'Company name')); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($partner, 'website')->label(\Yii::t('shop', 'Website')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($partner, 'message')
                        ->textarea(['rows' => 7])
                        ->label(\Yii::t('shop', 'Message')); ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('shop', 'Send'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        <?php endif; ?>
    </div>
</div>
