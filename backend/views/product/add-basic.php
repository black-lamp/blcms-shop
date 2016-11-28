<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $products_translation \bl\cms\shop\common\entities\Product
 * @var $selectedLanguage \bl\multilang\entities\Language
 */
use bl\cms\shop\common\entities\{
    Category, ProductAvailability, ProductCountryTranslation, Vendor
};
use bl\cms\shop\widgets\InputTree;
use marqu3s\summernote\Summernote;
use yii\helpers\{ArrayHelper, Html, Url};
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'method' => 'post',
    'action' => [
        'product/add-basic',
        'id' => $product->id,
        'languageId' => $selectedLanguage->id
    ]]);
?>



<!--SAVE BUTTON-->
<?= Html::submitInput(\Yii::t('shop', 'Save'), ['class' => 'btn btn-primary pull-right']); ?>

<!--BASIC-->
<div id="basic">

    <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>

    <!--NAME-->
    <?= $form->field($products_translation, 'title', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->label(\Yii::t('shop', 'Name'))
    ?>

    <div class="row">
        <!--CATEGORY-->
        <div class="col-md-6">
            <b><?= \Yii::t('shop', 'Category'); ?></b>
            <?=
            InputTree::widget([
                'className' => Category::className(),
                'form' => $form,
                'model' => $product,
                'attribute' => 'category_id',
                'languageId' => $selectedLanguage->id
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <div>
                <!--ARTICULUS-->
                <?= $form->field($product, 'articulus', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label(\Yii::t('shop', 'Articulus'))
                ?>
            </div>
            <div>
                <!--STANDART PRICE-->
                <?= $form->field($product, 'price', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textInput(['type' => 'number', 'step' => '0.01'])->label(\Yii::t('shop', 'Price'))
                ?>
            </div>
            <div>
                <!--COUNTRY-->
                <?= $form->field($product, 'country_id', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->dropDownList(
                    ['' => '-- no countries --'] +
                    ArrayHelper::map(ProductCountryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(), 'country_id', 'title')
                )->label(\Yii::t('shop', 'Country'));
                ?>
            </div>
            <div>
                <!--VENDOR-->
                <?= $form->field($product, 'vendor_id', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->dropDownList(
                    ['' => '-- no vendor --'] +
                    ArrayHelper::map(Vendor::find()->all(), 'id', 'title')
                )->label(\Yii::t('shop', 'Vendor'))
                ?>
            </div>
            <div>
                <!--AVAILABILITY-->
                <?= $form->field($product, 'availability', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->dropDownList(
                    ArrayHelper::map(ProductAvailability::find()->all(), 'id', 'translation.title')
                )->label(\Yii::t('shop', 'Availability'))
                ?>
            </div>
        </div>
    </div>


    <!--SALE-->
    <?= $form->field($product, 'sale', [
        'inputOptions' => [
            'class' => '']
    ])->checkbox(['class' => '']); ?>

    <!--SHORT DESCRIPTION-->
    <?= $form->field($products_translation, 'description', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->widget(Summernote::className())->label(\Yii::t('shop', 'Short description'));
    ?>

    <!-- FULL TEXT -->
    <?= $form->field($products_translation, 'full_text', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->widget(Summernote::className())->label(\Yii::t('shop', 'Full description'));
    ?>

    <!-- SEO -->
    <h2><?= \Yii::t('shop', 'SEO options'); ?></h2>
    <?= $form->field($products_translation, 'seoUrl', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->label('SEO URL')
    ?>
    <?= Html::button('Generate', ['class' => 'btn btn-primary pull-right', 'id' => 'generate-seo-url']); ?>

    <?= $form->field($products_translation, 'seoTitle', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->label(\Yii::t('shop', 'SEO title'))
    ?>
    <?= $form->field($products_translation, 'seoDescription', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO description'))
    ?>
    <?= $form->field($products_translation, 'seoKeywords', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO keywords'))
    ?>

    <a href="<?= Url::to(['/shop/product']); ?>">
        <?= Html::button(\Yii::t('shop', 'Cancel'), [
            'class' => 'btn btn-danger pull-right'
        ]); ?>
    </a>
    <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">
</div>

<?php $form::end(); ?>
