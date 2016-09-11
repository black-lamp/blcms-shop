<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $attribute bl\cms\shop\common\entities\ShopAttribute
 * @var $attributeTranslation bl\cms\shop\common\entities\ShopAttributeTranslation
 * @var $attributeType [] bl\cms\shop\common\entities\ShopAttributeType
 * @var $form yii\widgets\ActiveForm
 * @var $languages Language
 * @var $selectedLanguage Language
 * @var valueModel ShopAttributeValue
 * @var valueModelTranslation ShopAttributeValueTranslation
 */

use bl\multilang\entities\Language;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = Yii::t('shop', 'Create Shop Attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Shop Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (count($languages) > 1): ?>
    <div class="dropdown pull-right">
        <button class="btn btn-warning btn-xs dropdown-toggle" type="button"
                id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="true">
            <?= $selectedLanguage->name ?>
            <span class="caret"></span>
        </button>
        <?php if (count($languages) > 1): ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <?php foreach ($languages as $language): ?>
                    <li>
                        <a href="<?= Url::to([
                            'save',
                            'attrId' => $attribute->id,
                            'languageId' => $language->id]) ?>
                                                ">
                            <?= $language->name ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <div class="panel-body">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($attributeTranslation, 'title')->textInput() ?>

        <?php $options = (!$attribute->isNewRecord) ? ["disabled" => "disabled" ] : []?>
        <?= $form->field($attribute, 'type_id')->dropDownList(ArrayHelper::map($attributeType, 'id', 'title'), $options); ?>

        <div class="form-group">
            <?= Html::submitButton($attribute->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $attribute->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <hr>

    <?php if (!$attribute->isNewRecord) : ?>
    <div class="panel-body">

        <?= $this->render('add-value', [
            'dataProvider' => $dataProvider,
            'attribute' => $attribute,
            'selectedLanguage' => $selectedLanguage,
            'valueModelTranslation' => $valueModelTranslation,
            'valueModel' => $valueModel,
            'attributeTextureModel' => $attributeTextureModel
        ]); ?>

    </div>
    <?php endif; ?>