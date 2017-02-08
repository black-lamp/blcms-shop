<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

use dektrium\user\widgets\Connect;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\Profile $model
 */

$this->title = \Yii::t('cart', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("$(\"[data-toggle='tooltip']\").tooltip();");
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<section class="personal-area">
    <h1 class="title">
        <?= $this->title ?>
    </h1>
    <div class="row">
        <div class="col-sm-5">
            <?php $form = ActiveForm::begin([
                'id' => 'profile-form',
                'enableClientValidation' => false
            ]); ?>
            <?= $form->field($model, 'name', ['inputOptions' => ['class' => '']])
                ->label(\Yii::t('cart', 'Name')) ?>
            <?= $form->field($model, 'surname', ['inputOptions' => ['class' => '']])
                ->label(\Yii::t('cart', 'Surname')) ?>
            <?= $form->field($model, 'patronymic', ['inputOptions' => ['class' => '']])
                ->label(\Yii::t('cart', 'Patronymic')) ?>
            <?= $form->field($model, 'phone')
                ->widget(MaskedInput::className(), [
                    'mask' => '+38-(999)-999-99-99',
                    'options'=>['class' => '']
                ])
                ->label(\Yii::t('cart', 'Phone')); ?>
            <div class="form-group">
                <button type="submit">
                    <span class="btn-bg"></span>
                    <span class="btn-label">
                            <?= \Yii::t('cart', 'Save') ?>
                        </span>
                </button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row social-networks-wrapp">
        <div class="col-md-5">
            <?php $auth = Connect::begin([
                'baseAuthUrl' => ['/user/security/auth'],
                'accounts' => $model->user->accounts,
                'autoRender' => false,
                'popupMode' => false
            ]) ?>
            <div class="row">
                <h2 class="title">
                    <?= \Yii::t('cart', 'Connect to social networks') ?>
                </h2>
                <div class="social-icons">
                    <?php foreach ($auth->getClients() as $client): ?>
                        <?php $icon = Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]);
                        $tooltip_message = ($auth->isConnected($client)) ?
                            \Yii::t('cart', 'Disconnect') :
                            \Yii::t('cart', 'Connect to {name}', ['name' => $client->getTitle()]);
                        echo Html::a($icon, $auth->createClientUrl($client), [
                            'class' => 'social-btn',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'right',
                            'data-title' => $tooltip_message
                        ]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php Connect::end() ?>
        </div>
    </div>
</section>
