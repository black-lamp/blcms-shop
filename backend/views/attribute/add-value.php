<?php

use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\ShopAttributeValue;
use bl\cms\shop\common\entities\ShopAttributeValueColorTexture;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterRowOptions' => ['class' => 'm-b-sm m-t-sm'],
    'options' => [
        'class' => 'project-list'
    ],
    'tableOptions' => [
        'id' => 'my-grid',
        'class' => 'table table-hover'
    ],
    'summary' => "",

    'columns' => [
        'id',
        [
            'label' => \Yii::t('shop', 'Title'),
            'value' => 'translation.title',
        ],
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
                return $model->value;
            },
            'format' => 'raw'
        ],
    ]
]);
?>

<div class="shop-attribute-value-form">
    <?php $valueForm = ActiveForm::begin([
        'method' => 'post',
        'action' => [
            'attribute/add-value',
            'attrId' => $attribute->id,
            'languageId' => $selectedLanguage->id
        ],
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $valueForm->field($valueModelTranslation, 'title')->textInput(['maxlength' => true]) ?>

    <?php if ($attribute->type_id == 3) : ?>
        <?= $valueForm->field($attributeTextureModel, 'color')->input('color', ['class' => ""]); ?>

    <?php elseif ($attribute->type_id == 4) : ?>
        <?= $valueForm->field($attributeTextureModel, 'imageFile')->fileInput(); ?>
    <?php else : ?>
        <?= $valueForm->field($valueModelTranslation, 'value')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($valueModel->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $valueModel->isNewRecord ? 'pjax btn btn-success' : 'pjax btn btn-primary']) ?>
    </div>

    <?php $valueForm::end(); ?>

</div>

