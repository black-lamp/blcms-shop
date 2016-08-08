<?php
use bl\cms\shop\backend\assets\EditProductAsset;
use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ProductCountryTranslation;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\ProductVideo;
use bl\cms\shop\common\entities\Vendor;
use bl\multilang\entities\Language;
use marqu3s\summernote\Summernote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\JuiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $languages Language[]
 * @var $selectedLanguage Language
 * @var $product Product
 * @var $products_translation ProductTranslation
 * @var $params_translation ParamTranslation
 * @var $categories CategoryTranslation[]
 */

EditProductAsset::register($this);

$this->title = \Yii::t('shop', 'Edit product');
$newProductMessage = Yii::t('shop', 'You must save new product before this action');
?>

<div class="panel panel-default">

    <!--HEADER PANEL-->
    <div class="panel-heading">
        <i class="glyphicon glyphicon-list"></i>
        <?php if (!empty($product->id)) : ?>
            <?php if (!empty($products_translation->title)) : ?>
                <span>
                    <?= (!empty($product->translation->title)) ?
                        \Yii::t('shop', 'Edit product') . ' "' . $product->translation->title . '"' :
                        \Yii::t('shop', 'Edit product');
                    ?>
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

        <!-- LANGUAGES -->
        <? if (count($languages) > 1): ?>
            <div class="dropdown pull-right">
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
    </div>

    <!--BODY PANEL-->
    <div class="panel-body">

        <div id="tabs">
            <!-- TABS -->
            <ul class="nav nav-tabs nav-justified">
                <li><a data-toggle="tab" href="#basic"><?= \Yii::t('shop', 'Basic'); ?></a></li>
                <li><a data-toggle="tab" href="#photo"><?= \Yii::t('shop', 'Photo'); ?></a></li>
                <li><a data-toggle="tab" href="#video"><?= \Yii::t('shop', 'Video'); ?></a></li>
                <li><a data-toggle="tab" href="#prices"><?= \Yii::t('shop', 'Prices'); ?></a></li>
                <li><a data-toggle="tab" href="#params"><?= \Yii::t('shop', 'Params'); ?></a></li>
            </ul>

            <? $form = ActiveForm::begin(['method' => 'post', 'options' => ['class' => 'tab-content', 'enctype' => 'multipart/form-data']]); ?>

            <!--BASIC-->
            <div id="basic">

                <a href="<?= Url::to(['/shop/product']); ?>">
                    <?= Html::button(\Yii::t('shop', 'Close'), [
                        'class' => 'btn btn-danger pull-right'
                    ]); ?>
                </a>
                <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

                <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>
                <!--NAME-->
                <?= $form->field($products_translation, 'title', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label(\Yii::t('shop', 'Name'))
                ?>
                <!--CATEGORY-->
                <b><?= \Yii::t('shop', 'Category'); ?></b>
                <?= '<ul class="list-group ul-treefree ul-dropfree">'; ?>
                <?= '<li class="list-group-item"><input type="radio" checked name="Product[category_id]" value="" id="null"><label for="null">' . \Yii::t("shop", "Without parent") . '</label>'; ?>
                <?= CategoryTranslation::treeRecoursion($categoriesTree, $product->category_id, 'Product[category_id]'); ?>
                <?= '</ul>'; ?>

                <!--STANDART PRICE-->
                <?= $form->field($product, 'price', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textInput(['type' => 'number', 'step' => '0.01'])->label(\Yii::t('shop', 'Price'))
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
                    <?= Html::button(\Yii::t('shop', 'Close'), [
                        'class' => 'btn btn-danger pull-right'
                    ]); ?>
                </a>
                <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

            </div>

            <? $form::end(); ?>

            <!--PHOTO-->
            <div id="photo">
                <? if (!$product->isNewRecord): ?>
                    <? Pjax::begin([
                        'enablePushState' => false,
                        'timeout' => 10000
                    ]);
                    ?>
                    <?= $this->render('/product/add-image', [
                        'product' => $product,
                        'image_form' => new ProductImageForm()
                    ]) ?>
                    <? Pjax::end(); ?>
                    <? else : ?>
                    <p>
                        <?= $newProductMessage; ?>
                    </p>
                <? endif; ?>
            </div>

            <!--VIDEO-->
            <div id="video">
                <? if (!$product->isNewRecord): ?>
                    <? Pjax::begin([
                        'enablePushState' => false,
                        'timeout' => 10000
                    ]);
                    ?>
                    <?= $this->render('/product/add-video', [
                        'product' => $product,
                        'video_form' => new ProductVideo(),
                        'video_form_upload' => new ProductVideoForm()
                    ]) ?>
                    <? Pjax::end(); ?>
                    <? else : ?>
                    <p>
                        <?= $newProductMessage; ?>
                    </p>
                <? endif; ?>
            </div>

            <!--PRODUCT PRICES-->
            <div id="prices">
                <? if (!$product->isNewRecord): ?>
                    <? Pjax::begin([
                        'linkSelector' => '.price',
                        'enablePushState' => false,
                        'timeout' => 5000
                    ]);
                    ?>
                    <?= $this->render('add-price', [
                        'priceList' => $product->prices,
                        'priceModel' => new ProductPrice(),
                        'priceTranslationModel' => new ProductPriceTranslation(),
                        'product' => $product,
                        'languages' => $languages,
                        'language' => $selectedLanguage
                    ]) ?>
                    <? Pjax::end(); ?>
                    <? else : ?>
                    <p>
                        <?= $newProductMessage; ?>
                    </p>
                <? endif; ?>
            </div>

            <!--PARAMS-->
            <div id="params">
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
                    <? else : ?>
                    <p>
                        <?= $newProductMessage; ?>
                    </p>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>