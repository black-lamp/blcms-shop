<?php
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\SaleType;
use bl\multilang\entities\Language;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var Product $product
* @var ProductPrice $priceModel 
* @var ProductPriceTranslation $priceTranslationModel
* @var ProductPrice[] $priceList
* @var Language[] $languages
* @var Language $language
*/
?>

<? $form = ActiveForm::begin([
    'action' => [
        'price/add',
        'productId' => $product->id,
        'languageId' => $language->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'price',
        'data-pjax' => true
    ]
]) ?>

<? if (!empty($priceList)): ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Title'); ?></th>
            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Price'); ?></th>
            <th class="col-md-2 text-center"><?= \Yii::t('shop', 'Sale type'); ?></th>
            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Sale'); ?></th>
            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Control'); ?></th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($priceList as $price): ?>
            <tr class="text-center">
                <? if (!empty($price->translation)): ?>
                    <td><?= $price->translation->title ?></td>
                <? endif; ?>
                <td><?= $price->price ?></td>
                <td><?= $price->type->title ?></td>
                <td><?= $price->sale ?></td>
                <td class="text-center">
                    <?= Html::a('', [
                        'price/remove',
                        'priceId' => $price->id,
                        'productId' => $product->id,
                        'languageId' => $language->id
                    ],
                        [
                            'class' => 'price glyphicon glyphicon-remove text-danger btn btn-default btn-sm'
                        ]
                    ) ?>
                </td>
            </tr>
        <? endforeach; ?>
            <tr>
                <!--Title-->
                <td>
                    <?= $form->field($priceTranslationModel, 'title')->label(false) ?>
                </td>
                <!--Price-->
                <td>
                    <?= $form->field($priceModel, 'price')->textInput(['type' => 'number'])->label(false) ?>
                </td>
                <!--Sale type-->
                <td>
                    <?= $form->field($priceModel, 'sale_type_id')
                        ->dropDownList(
                            ['' => '--none--'] +
                            ArrayHelper::map(SaleType::find()->asArray()->all(), 'id', 'title')
                        )->label(false)
                    ?>
                </td>
                <!--Sale-->
                <td>
                    <?= $form->field($priceModel, 'sale')->textInput(['type' => 'number'])->label(false) ?>
                </td>
                <td>
                    <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
                </td>
            </tr>
<? $form->end() ?>

        </tbody>
    </table>
<? endif; ?>