<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $userGroupTranslation \bl\cms\shop\common\components\user\models\UserGroupTranslation
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = \Yii::t('shop', 'Change user group');
?>

<div class="ibox">
    <div class="ibox-title">
        <h1>
            <?= $this->title; ?>
        </h1>
    </div>
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal'
        ]); ?>

        <?= $form->field($userGroupTranslation, 'title') ?>
        <?= $form->field($userGroupTranslation, 'description') ?>


        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
                <?= Html::a(\Yii::t('shop', 'Cancel'), Url::to('show-user-groups'), [
                    'class' => 'btn btn-danger btn-block'
                ]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>