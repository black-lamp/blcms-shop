<?php

use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\ShopAttributeValueColorTexture;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<?php Pjax::begin([
    'enablePushState' => false,
    'timeout' => 5000
]);

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterRowOptions' => ['class' => 'm-b-sm m-t-sm'],
    'options' => [
        'class' => 'table table-hover table-striped table-bordered'
    ],
    'tableOptions' => [
        'id' => 'my-grid',
        'class' => 'table table-hover'
    ],
    'summary' => "",

    'columns' => [
        'id',
        [
            'attribute' => 'value',
            'value' => function($model) {

                $attribute = ShopAttribute::findOne($model->attribute_id);
                if ($attribute->type_id == 3) {

                    $color = ShopAttributeValueColorTexture::findOne($model->translation->value)->color;
                    return Html::tag('div', '' , ['style' => 'width: 50px; height: 50px; background-color:' . $color]);
                }
                if (ShopAttribute::findOne($model->attribute_id)->type_id == 4) {
                    return ShopAttributeValueColorTexture::getTexture($model->translation->value);
                }
                return $model->translation->value;
            },
            'format' => 'raw'
        ],
    ]
]);


?>

<div class="shop-attribute-value-form">

    <?php $valueForm = ActiveForm::begin([
        'method' => 'post',
        'options' => ['data-pjax' => true, 'enctype' => 'multipart/form-data'],
        'action' => [
            'attribute/add-value',
            'attrId' => $attribute->id,
            'languageId' => $selectedLanguage->id
        ],
    ]); ?>

    <?php if ($attribute->type_id == 3) : ?>
        <?= $valueForm->field($attributeTextureModel, 'color')->input('color', ['class' => "col-md-10"])->label(false); ?>

    <?php elseif ($attribute->type_id == 4) : ?>
        <?= $valueForm->field($attributeTextureModel, 'imageFile')->fileInput(['class' => "col-md-10"])->label(false); ?>
    <?php else : ?>
        <?= $valueForm->field($valueModelTranslation, 'value')
            ->textInput(['maxlength' => true, 'class' => "form-control col-md-10"])->label(false) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($valueModel->isNewRecord ?
            Yii::t('shop', 'Add') : Yii::t('shop', 'Update'), ['class' => 'pjax btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php Pjax::end(); ?>


