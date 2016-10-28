<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model \bl\cms\shop\common\entities\Currency
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Yii::t('shop', 'Currency');
?>

<div class="panel panel-default">

    <div class="panel-heading">
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= Html::encode($this->title); ?>
        </h5>
    </div>

    <div class="panel-body">

        <?php
        $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'value')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Edit'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('shop', 'Close'), Url::toRoute('/shop/currency'), ['class' => 'btn btn-danger']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>


