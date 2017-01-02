<?php
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\PriceDiscountType;
use bl\cms\shop\widgets\ManageButtons;
use bl\multilang\entities\Language;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var Product $product
 * @var ProductPrice $priceModel
 * @var ProductPriceTranslation $priceTranslationModel
 * @var ProductPrice[] $priceList
 * @var Language[] $languages
 * @var Language $language
 */
?>
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'action' => [
        'add-price',
        'id' => $product->id,
        'languageId' => $language->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'price',
        'data-pjax' => true
    ]
]) ?>

<h2><?= \Yii::t('shop', 'Prices'); ?></h2>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="col-md-1"><?= \Yii::t('shop', 'Position'); ?></th>
        <th class="col-md-2"><?= \Yii::t('shop', 'Articulus'); ?></th>
        <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Title'); ?></th>
        <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Price'); ?></th>
        <th class="col-md-2 text-center"><?= \Yii::t('shop', 'Sale type'); ?></th>
        <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Discount'); ?></th>
        <th class="col-md-2 text-center"><?= \Yii::t('shop', 'Control'); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <!--Articulus-->
        <td>
            <?= $form->field($priceModel, 'articulus')->label(false) ?>
        </td>
        <!--Title-->
        <td>
            <?= $form->field($priceTranslationModel, 'title')->label(false) ?>
        </td>
        <!--Price-->
        <td>
            <?= $form->field($priceModel, 'price')->textInput(['type' => 'number', 'step' => '0.01'])->label(false) ?>
        </td>
        <!--Sale type-->
        <td>
            <?= $form->field($priceModel, 'sale_type_id')
                ->dropDownList(
                    ['' => '--none--'] +
                    ArrayHelper::map(PriceDiscountType::find()->asArray()->all(), 'id', 'title')
                )->label(false)
            ?>
        </td>
        <!--Sale-->
        <td>
            <?= $form->field($priceModel, 'sale')->textInput(['type' => 'number', 'step' => '0.01'])->label(false) ?>
        </td>
        <td class="text-center">
            <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
        </td>
    </tr>
    <?php if (!empty($priceList)): ?>
        <?php foreach ($priceList as $price): ?>
            <tr>
                <td>
                    <?= Html::a(
                        '',
                        Url::toRoute(['price-up', 'id' => $price->id, 'languageId' => $language->id]),
                        [
                            'class' => 'pjax fa fa-chevron-up'
                        ]
                    ) .
                    $price->position .
                    Html::a(
                        '',
                        Url::toRoute(['price-down', 'id' => $price->id, 'languageId' => $language->id]),
                        [
                            'class' => 'pjax fa fa-chevron-down'
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?= $price->articulus ?>
                </td>
                <td>
                    <?php if (!empty($price->translation)): ?>
                        <?= $price->translation->title ?>
                    <?php endif; ?>
                </td>
                <td><?= $price->price ?></td>
                <td><?= $price->type->title ?? '' ?></td>
                <td><?= $price->sale ?? '' ?></td>
                <td class="text-center">
                    <?= ManageButtons::widget([
                        'model' => $price,
                        'action' => 'edit-price',
                        'deleteUrl' => Url::toRoute(['remove-price',
                            'priceId' => $price->id,
                            'id' => $product->id,
                            'languageId' => $language->id])
                    ]); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<?php $form->end() ?>
<?php Pjax::end(); ?>

