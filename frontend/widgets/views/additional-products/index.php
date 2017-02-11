<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $productAdditionalProducts \bl\cms\shop\common\entities\ProductAdditionalProduct[]
 * @var $form \yii\widgets\ActiveForm
 * @var $model \yii\base\Model
 * @var $modelAttribute string
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php if (!empty($productAdditionalProducts)) : ?>

    <?= $form->field($model, $modelAttribute)
        ->checkboxList(
            ArrayHelper::map(
                $productAdditionalProducts, function ($model) {
                    return $model->additionalProduct->id;
                },
                function ($model) {
                    $price = \Yii::$app->formatter->asCurrency($model->additionalProduct->price->getDiscountPrice());
                    return $model->additionalProduct->translation->title . " - $price";
                }),
            [
                'class' => 'checkbox',
                'template'=>'<div class="item">{input}{label}</div>',
                'item' => function($index, $label, $name, $checked, $value) {
                    return "<div class='checkbox checkbox-warning'>
                            <input type='checkbox' id='" . $index . "' name='$name' value='$value' >
                            <label for='" . $index . "'>My Label</label></div>";
                },
            ]
        ); ?>

<?php endif; ?>
