<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $price \bl\cms\shop\common\entities\ProductPrice
 * @var $priceTranslation \bl\cms\shop\common\entities\ProductImageTranslation
 * @var $selectedLanguage \bl\multilang\entities\Language
 */
use bl\cms\shop\common\entities\PriceDiscountType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'action' => [
        'product/edit-price',
        'id' => $price->id,
        'languageId' => $selectedLanguage->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'tab-content',
        'data-pjax' => true
    ]
]);
?>

<!--Articulus-->
<td>
    <?= $form->field($price, 'articulus')->label(false) ?>
</td>
<!--Title-->
<td>
    <?= $form->field($priceTranslation, 'title')->label(false) ?>
</td>
<!--Price-->
<td>
    <?= $form->field($price, 'price')->textInput(['type' => 'number', 'step' => '0.01'])->label(false) ?>
</td>
<!--Sale type-->
<td>
    <?= $form->field($price, 'sale_type_id')
        ->dropDownList(
            ['' => '--none--'] +
            ArrayHelper::map(PriceDiscountType::find()->asArray()->all(), 'id', 'title')
        )->label(false)
    ?>
</td>
<!--Sale-->
<td>
    <?= $form->field($price, 'sale')->textInput(['type' => 'number', 'step' => '0.01'])->label(false) ?>
</td>
<td>
    <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
</td>


<?php $form->end(); ?>
