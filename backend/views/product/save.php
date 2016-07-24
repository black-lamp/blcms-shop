<?php
use bl\cms\shop\backend\assets\EditProductAsset;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ProductCountryTranslation;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Vendor;
use bl\multilang\entities\Language;
use dosamigos\tinymce\TinyMce;
use marqu3s\summernote\Summernote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $languages Language[] */
/* @var $selectedLanguage Language */
/* @var $product Product */
/* @var $products_translation ProductTranslation */
/* @var $params_translation ParamTranslation */
/* @var $categories Category[] */

EditProductAsset::register($this);
$this->title = \Yii::t('shop', 'Edit product');
?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="glyphicon glyphicon-list"></i>
            <?php if (!empty($product->id)) : ?>
                <?php if (!empty($products_translation->title)) : ?>
                    <span>
                    <?= \Yii::t('shop', 'Edit product'); ?>
                </span>
                <?php else: ?>
                    <span>
                    <?= \Yii::t('shop', 'Add product translation'); ?>
                </span>
                <?php endif; ?>
            <?php else : ?>
                <span>
                    <?= \Yii::t('shop', 'Add new product'); ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="panel-body">

            <!-- TABS -->
            <div id="tabs">
                <ul class="nav nav-tabs nav-justified">
                    <li class="active"><a data-toggle="tab" href="#basic"><?= \Yii::t('shop', 'Basic'); ?></a></li>
                    <li><a data-toggle="tab" href="#seo"><?= \Yii::t('shop', 'SEO data'); ?></a></li>
                    <li><a data-toggle="tab" href="#media"><?= \Yii::t('shop', 'Photo/Video'); ?></a></li>
                    <li><a data-toggle="tab" href="#prices"><?= \Yii::t('shop', 'Prices'); ?></a></li>
                    <li><a data-toggle="tab" href="#params"><?= \Yii::t('shop', 'Params'); ?></a></li>
                </ul>

                <? $form = ActiveForm::begin(['method' => 'post', 'options' => ['class' => 'tab-content', 'enctype' => 'multipart/form-data']]); ?>
                <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

                <div id="basic">
                    <!--BASIC-->
                    <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>
                    <!-- LANGUAGES -->
                    <? if (count($languages) > 1): ?>
                        <div class="dropdown">
                            <button class="btn btn-warning btn-xs dropdown-toggle" type="button"
                                    id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="true">
                                <?= $selectedLanguage->name ?>
                                <span class="caret"></span>
                            </button>
                            <? if (count($languages) > 1): ?>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <? foreach ($languages as $language): ?>
                                        <li>
                                            <a href="
                                        <?= Url::to([
                                                'save',
                                                'productId' => $product->id,
                                                'languageId' => $language->id]) ?>
                                                ">
                                                <?= $language->name ?>
                                            </a>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            <? endif; ?>
                        </div>
                    <? endif; ?>

                    <!--NAME-->
                    <?= $form->field($products_translation, 'title', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label(\Yii::t('shop', 'Name'))
                    ?>
                    <!--CATEGORY-->
                    <?= $form->field($product, 'category_id', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->dropDownList(
                        ['' => '-- no categories --'] +
                        ArrayHelper::map(CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(), 'category_id', 'title')
                    )->label(\Yii::t('shop', 'Category'));
                    ?>
                    <?= $form->field($product, 'price', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label(\Yii::t('shop', 'Price'))
                    ?>
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
                    ])->widget(Summernote::className())->label(\Yii::t('shop', 'Full description'))
                    ?>

                    <!-- CHARACTERISTICS -->
                    <hr>
                    <h2>Characteristics & doses</h2>
                    <?= $form->field($products_translation, 'characteristics', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->widget(Summernote::className())->label('Characteristics')
                    ?>

                    <!--DOSES-->
                    <?= $form->field($products_translation, 'dose', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->widget(Summernote::className())->label('Doses')
                    ?>

                    <!-- EXPORT -->
                    <?= $form->field($product, 'export', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->checkbox(['class' => 'i-checks', 'checked ' => ($product->export) ? '' : false])
                    ?>
                </div>

                <!-- SEO -->
                <div id="seo">
                    <h2><?= \Yii::t('shop', 'SEO options'); ?></h2>
                    <?= $form->field($products_translation, 'seoUrl', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('SEO URL')
                    ?>
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
                </div>

                <!--MEDIA-->
                <div id="media">
                    <h2>
                        <?= \Yii::t('shop', 'Photo/Video'); ?>
                    </h2>
                    <? if(!empty($product->image_name)): ?>
                        <?= Html::img($product->getThumbImage()) ?>
                    <? endif; ?>

                    <?= $form->field($product, 'imageFile')->fileInput() ?>
                </div>
                <? ActiveForm::end(); ?>

                <!--PRODUCT PRICES-->
                <div id="prices">
                    <h2>
                        <?= \Yii::t('shop', 'Prices'); ?>
                    </h2>
                    <? if (!$product->isNewRecord): ?>
                        <? Pjax::begin([
                            'linkSelector' => '.price',
                            'enablePushState' => false,
                            'timeout' => 5000
                        ]);
                        ?>
                        <?= $this->render('/price/add', [
                            'priceList' => $product->prices,
                            'priceModel' => new ProductPrice(),
                            'priceTranslationModel' => new ProductPriceTranslation(),
                            'product' => $product,
                            'languages' => $languages,
                            'language' => $selectedLanguage
                        ]) ?>
                        <? Pjax::end(); ?>
                    <? endif; ?>
                </div>

                <!--PARAMS-->
                <div id="params">
                    <h2>
                        <?= \Yii::t('shop', 'Params'); ?>
                    </h2>
                    <? if (!$product->isNewRecord): ?>
                        <? Pjax::begin([
                            'linkSelector' => '.param',
                            'enablePushState' => false,
                            'timeout' => 5000
                        ]);
                        ?>
                        <?= $this->render('/product/add-param', [
                            'product' => $product,
                            'param' => new Param(),
                            'param_translation' => new ParamTranslation(),
                            'languages' => Language::findAll(['active' => true]),
                            'selectedLanguage' => Language::findOne($selectedLanguage->id),
                            'products' => Product::find()->with('translations')->all(),
                            'productId' => $product->id
                        ]);
                        ?>
                        <? Pjax::end(); ?>
                    <? endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>

<script>
    $(function () {
        $("#tabs").tabs();
    });
</script>

