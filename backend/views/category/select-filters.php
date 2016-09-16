<?php
use bl\articles\backend\assets\TabsAsset;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\FilterInputType;
use bl\cms\shop\common\entities\FilterType;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $filters Filter
 * @var $newFilter Filter
 * @var $languageId Language
 */

TabsAsset::register($this);
?>

<?php $form = ActiveForm::begin(); ?>
    <h2><?= \Yii::t('shop', 'Filters'); ?></h2>
    <p>
        <?= \Yii::t('shop', "In this part of the settings you can add or delete filtering methods for the list of category's products"); ?>
    </p>

    <table class="table table-bordered table-condensed table-stripped table-hover">
        <tr>
            <th class="text-center col-md-5">
                <?= \Yii::t('shop', 'Filter type'); ?>
            </th>
            <th class="text-center col-md-5">
                <?= \Yii::t('shop', 'Field type'); ?>
            </th>
            <th class="text-center col-md-2">
                <?= \Yii::t('shop', 'Delete'); ?>
            </th>
        </tr>
        <?php foreach ($filters as $filter) : ?>
            <?php if (!empty($filter)) : ?>
                <tr>
                    <td>
                        <?= Yii::t('shop', FilterType::findOne($filter->filter_type)->title); ?>
                    </td>
                    <td>
                        <?= Yii::t('shop', FilterInputType::findOne($filter->input_type)->title); ?>
                    </td>
                    <td class="text-center">
                        <?= Html::a(
                            '',
                            Url::toRoute(['delete-filter', 'id' => $filter->id]),
                            ['class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm']
                        ); ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>

    <table class="table table-bordered table-condensed table-stripped table-hover">
        <tr>
            <td class="col-md-5">
                <?= $form->field($newFilter, 'filter_type')->dropDownList(
                    ArrayHelper::map(FilterType::find()->all(), 'id', function ($model) {
                        return \Yii::t('shop', $model->title);
                    })
                )->label(false); ?>
            </td>
            <td class="col-md-5">
                <?= $form->field($newFilter, 'input_type')->dropDownList(
                    ArrayHelper::map(FilterInputType::find()->all(), 'id', function ($model) {
                        return \Yii::t('shop', $model->title);
                    })
                )->label(false); ?>
            </td>
            <td class="col-md-2 text-center">
                <?= Html::submitButton(Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>

    <hr>

<?= Html::tag('h3', \Yii::t('shop', 'Types of fields') . ':'); ?>

    <table class="table table-bordered table-condensed table-stripped table-hover">
        <tr>
            <td class="text-center col-md-5">
                <?= Html::beginForm() . Html::radio('info', true) . Html::radio('info') . Html::endForm(); ?>
            </td>
            <td class="col-md-7">
                <?= \Yii::t('shop', 'Radio button'); ?>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <?= Html::checkbox('', true) . Html::checkbox(''); ?>
            </td>
            <td>
                <?= \Yii::t('shop', 'Checkbox'); ?>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <?= Html::dropDownList('', '', [\Yii::t('shop', 'On'), \Yii::t('shop', 'Off')]); ?>
            </td>
            <td>
                <?= \Yii::t('shop', 'Drop down list'); ?>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <?= Html::textInput('', '', ['placeholder' => \Yii::t('shop', 'Text input')]); ?>
            </td>
            <td>
                <?= \Yii::t('shop', 'Text input'); ?>
            </td>
        </tr>
    </table>

<?php ActiveForm::end(); ?>