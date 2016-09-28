<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\OrderStatus
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('shop', 'Create order status');
?>
<div class="panel panel-default">

    <div class="panel-heading">

    <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <?= Html::a(Yii::t('shop', 'Close'), Url::toRoute('/shop/order-status'), ['class' => 'm-r-xs btn btn-danger btn-xs pull-right']); ?>
            <?= Html::submitButton(Yii::t('shop', 'Save'), ['class' => 'btn btn-primary btn-xs m-r-xs pull-right']); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>