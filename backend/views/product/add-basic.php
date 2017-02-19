<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var array $prices
 * @var integer $selectedLanguageId
 * @var \bl\cms\shop\common\entities\Product $product
 * @var \bl\cms\shop\common\entities\ProductTranslation $productTranslation
 */
use bl\cms\shop\common\components\user\models\UserGroup;
use bl\cms\shop\common\entities\{
    PriceDiscountType, Product, ProductAvailability, ProductCountryTranslation, Vendor
};
use marqu3s\summernote\Summernote;
use yii\helpers\{
    ArrayHelper, Html, Url
};
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'method' => 'post',
    'action' => [
        'product/save',
        'id' => $product->id,
        'languageId' => $selectedLanguageId
    ]]);
?>

<!--SAVE BUTTON-->
<?= Html::submitInput(\Yii::t('shop', 'Save'), ['class' => 'btn btn-xs btn-primary m-r-xs pull-right']); ?>

<!--BASIC-->
<div id="basic">

    <!--MODERATION-->
    <?php if (Yii::$app->user->can('moderateProductCreation') && $product->status == Product::STATUS_ON_MODERATION) : ?>
        <h2><?= \Yii::t('shop', 'Moderation'); ?></h2>
        <p class="text-warning"><?= \Yii::t('shop', 'This product\'s status is "on moderation". You may accept or decline it.'); ?></p>
        <?= Html::a(\Yii::t('shop', 'Accept'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_SUCCESS]), ['class' => 'btn btn-primary btn-xs']); ?>
        <?= Html::a(\Yii::t('shop', 'Decline'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_DECLINED]), ['class' => 'btn btn-danger btn-xs']); ?>
        <hr>
    <?php endif; ?>

    <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>

    <?php if (!\Yii::$app->user->can('createProductWithoutModeration') && $product->status == Product::STATUS_ON_MODERATION): ?>
        <p class="text-danger"><?= \Yii::t('shop', 'The product has not passed moderation yet'); ?></p>
    <?php endif; ?>

    <!--NAME-->
    <?= $form->field($productTranslation, 'title', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->label(\Yii::t('shop', 'Name'))
    ?>

    <div class="row">
        <div class="col-md-6">
            <div>
                <!--SKU-->
                <?= $form->field($product, 'sku', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label(\Yii::t('shop', 'SKU'))
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
                    ArrayHelper::map(ProductCountryTranslation::find()->where(['language_id' => $selectedLanguageId])->all(), 'country_id', 'title')
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
                ); ?>
            </div>
            <div>
                <!--NUMBER-->
                <?= $form->field($product, 'number', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textInput()
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <!--CATEGORY-->
            <b><?= \Yii::t('shop', 'Category'); ?></b>
            <?= \bl\cms\shop\widgets\InputTree::widget([
                'className' => \bl\cms\shop\common\entities\Category::className(),
                'form' => $form,
                'model' => $product,
                'attribute' => 'category_id',
                'languageId' => $selectedLanguageId
            ]);
            ?>
        </div>
    </div>

    <div class="">
        <!--SALE-->
        <div style="display: inline-block;">
            <?= $form->field($product, 'sale', [
                'inputOptions' => [
                    'class' => '']
            ])->checkbox(); ?>
        </div>

        <!--POPULAR-->
        <div style="display: inline-block;">
            <?= $form->field($product, 'popular', [
                'inputOptions' => [
                    'class' => '']
            ])->checkbox(); ?>
        </div>
    </div>

    <!--BASE PRICE-->
    <?php if (!empty($prices)): ?>
        <hr>
        <h2><?= \Yii::t('shop', 'Base price'); ?></h2>
        <?php foreach ($prices as $key => $price) : ?>
            <h3 class="text-center"><?= UserGroup::findOne($key)->translation->title; ?></h3>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($price, "[$key]price")->textInput(['type' => 'number', 'step' => '0.01']); ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($price, "[$key]discount_type_id")
                        ->dropDownList(
                            ['' => '--none--'] +
                            ArrayHelper::map(PriceDiscountType::find()->asArray()->all(), 'id', 'title'));
                    ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($price, "[$key]discount")->textInput(['type' => 'number', 'step' => '0.01']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>
    <h2><?=\Yii::t('shop', 'Product description'); ?></h2>
    <!--SHORT DESCRIPTION-->
    <?= $form->field($productTranslation, 'description', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->widget(Summernote::className())->label(\Yii::t('shop', 'Short description'));
    ?>

    <!--FULL TEXT-->
    <?= $form->field($productTranslation, 'full_text', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->widget(Summernote::className())->label(\Yii::t('shop', 'Full description'));
    ?>

    <!--SEO-->
    <hr>
    <h2><?= \Yii::t('shop', 'SEO options'); ?></h2>
    <div class="seo-url">
        <?= $form->field($productTranslation, 'seoUrl', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->label('SEO URL')
        ?>
        <?= Html::button(\Yii::t('shop', 'Generate'), [
            'id' => 'generate-seo-url',
            'class' => 'btn btn-primary btn-generate',
            'url' => Url::to('generate-seo-url')
        ]); ?>
    </div>

    <?= $form->field($productTranslation, 'seoTitle', [
        'inputOptions' => [
            'class' => 'form-control'
        ]
    ])->label(\Yii::t('shop', 'SEO title'))
    ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($productTranslation, 'seoDescription', [
                'inputOptions' => [
                    'class' => 'form-control'
                ]
            ])->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO description'))
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($productTranslation, 'seoKeywords', [
                'inputOptions' => [
                    'class' => 'form-control'
                ]
            ])->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO keywords'))
            ?>
        </div>
    </div>

    <div class="ibox">
        <a href="<?= Url::to(['/shop/product']); ?>">
            <?= Html::button(\Yii::t('shop', 'Cancel'), [
                'class' => 'btn btn-xs btn-danger pull-right'
            ]); ?>
        </a>
        <input type="submit" class="btn btn-xs btn-primary m-r-xs pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">
    </div>
</div>

<?php $form::end(); ?>
