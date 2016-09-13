<?php
use bl\articles\backend\assets\TabsAsset;
use bl\cms\shop\common\entities\Filter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $filters Filter
 */

TabsAsset::register($this);

$this->title = \Yii::t('shop', 'Selecting filters');
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($filters, 'filter_by_vendor')->checkbox(); ?>
<?= $form->field($filters, 'filter_by_country')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('shop', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>