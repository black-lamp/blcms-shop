<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $attribute bl\cms\shop\common\entities\ShopAttribute
 * @var $attributeTranslation bl\cms\shop\common\entities\ShopAttributeTranslation
 * @var $attributeType[] bl\cms\shop\common\entities\ShopAttributeType
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('shop', 'Create Shop Attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Shop Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-attribute-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="shop-attribute-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($attributeTranslation, 'title')->textInput() ?>
        <?= $form->field($attribute, 'type_id')->dropDownList(ArrayHelper::map($attributeType,'id', 'title')); ?>

        <div class="form-group">
            <?= Html::submitButton($attribute->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $attribute->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>