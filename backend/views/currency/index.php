<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model \bl\cms\shop\common\entities\Currency
 * @var $rates \bl\cms\shop\common\entities\Currency
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

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'value')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <table class="table table-hover">
            <tr>
                <th class="col-md-1">#</th>
                <th class="col-md-7"><?= Yii::t('shop', 'Rate'); ?></th>
                <th class="col-md-2"><?= Yii::t('shop', 'Date'); ?></th>
                <th class="col-md-2"></th>
            </tr>

            <?php foreach ($rates as $rate) : ?>
                <tr>
                    <td>
                        <?=$rate->id; ?>
                    </td>
                    <td>
                        <?=$rate->value; ?>
                    </td>
                    <td>
                        <?=$rate->date; ?>
                    </td>
                    <td>
                        <?= Html::a('',
                            Url::to(['update', 'id' => $rate->id])
                            , ['class' => 'glyphicon glyphicon-edit btn btn-success pull-right btn-xs']); ?>

                        <?= Html::a('',
                            Url::to(['remove', 'id' => $rate->id])
                        , ['class' => 'glyphicon glyphicon-remove btn btn-danger pull-right btn-xs']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>


