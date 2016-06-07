<?php
use bl\cms\shop\common\entities\CategoryTranslation;
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
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $languages Language[] */
/* @var $selectedLanguage Language */
/* @var $product Product */
/* @var $products_translation ProductTranslation */
/* @var $params_translation ParamTranslation */
/* @var $categories Category[] */

$this->title = 'Edit product';
?>

<? $form = ActiveForm::begin(['method' => 'post']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="glyphicon glyphicon-list"></i>
                    <?= 'Product' ?>
                    <?= 'Product' ?>
                </div>
                <div class="panel-body">
                    <? if (count($languages) > 1): ?>
                        <div class="dropdown">
                            <button class="btn btn-warning btn-xs dropdown-toggle" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group field-validarticleform-category_id required has-success">

                                <!--CATEGORIES-->
                                <?= $form->field($product, 'category_id', [
                                    'inputOptions' => [
                                        'class' => 'form-control'
                                    ]
                                ])->dropDownList(
                                    ['' => '-- no categories --'] +
                                    ArrayHelper::map(CategoryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(), 'category_id', 'title')
                                )->label('Category')
                                ?>

                            </div>

                            <!--COUNTRY-->
                            <?= $form->field($product, 'country_id', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->dropDownList(
                                ['' => '-- no countries --'] +
                                ArrayHelper::map(ProductCountryTranslation::find()->where(['language_id' => $selectedLanguage->id])->all(), 'country_id', 'title')
                            )->label('Country')
                            ?>

                            <!--VENDOR-->
                            <?= $form->field($product, 'vendor_id', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->dropDownList(
                                ['' => '-- no vendor --'] +
                                ArrayHelper::map(Vendor::find()->all(), 'id', 'title')
                            )->label('Vendor')
                            ?>

                            <!--TITLE-->
                            <?= $form->field($products_translation, 'title', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->label('Title')
                            ?>

                            <!--DESCRIPTION-->
                            <?= $form->field($products_translation, 'description', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->widget(TinyMce::className(), [
                                'options' => ['rows' => 10],
                                'language' => 'ru',
                                'clientOptions' => [
                                    'relative_urls' => false,
                                    'plugins' => [
                                        'textcolor colorpicker',
                                        "advlist autolink lists link charmap print preview anchor",
                                        "searchreplace visualblocks code fullscreen",
                                        "insertdatetime media table contextmenu paste",
                                        'image'
                                    ],
                                    'toolbar' => Yii::$app->params['toolbar'],
                                ]
                            ])->label('Description')
                            ?>
                        </div>

                        <div class="col-md-3 text-center">
                            <!--IMAGE-->
                            <h2>Image</h2>
                            <? if(!empty($product->image_name)): ?>
                                <?= Html::img($product->getThumbImage()) ?>
                            <? endif; ?>

                            <?= $form->field($product, 'imageFile')->fileInput() ?>

                            <!--EXPORT-->
                            <hr>
                            <?= $form->field($product, 'export')
                                ->checkbox(['class' => 'i-checks','checked ' => ''])
                            ?>
                        </div>
                    </div>

                    <!-- FULL TEXT FIELD -->
                    <?= $form->field($products_translation, 'full_text', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->widget(TinyMce::className(), [
                        'options' => ['rows' => 20],
                        'language' => 'ru',
                        'clientOptions' => [
                            'relative_urls' => false,
                            'plugins' => [
                                'textcolor colorpicker',
                                "advlist autolink lists link charmap print preview anchor",
                                "searchreplace visualblocks code fullscreen",
                                "insertdatetime media table contextmenu paste",
                                'image'
                            ],
                            'toolbar' => Yii::$app->params['toolbar'],
                        ]
                    ])->label('Full text')
                    ?>

                    <!-- CHARACTERISTICS -->
                    <hr>
                    <h2>Characteristics & doses</h2>
                    <?= $form->field($products_translation, 'characteristics', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->widget(TinyMce::className(), [
                        'options' => ['rows' => 10],
                        'language' => 'ru',
                        'clientOptions' => [
                            'relative_urls' => false,
                            'plugins' => [
                                'textcolor colorpicker',
                                "advlist autolink lists link charmap print preview anchor",
                                "searchreplace visualblocks code fullscreen",
                                "insertdatetime media table contextmenu paste",
                                'image'
                            ],
                            'toolbar' => Yii::$app->params['toolbar'],
                        ]
                    ])->label('Characteristics')
                    ?>

                    <!--DOSES-->
                    <?= $form->field($products_translation, 'dose', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->widget(TinyMce::className(), [
                        'options' => ['rows' => 10],
                        'language' => 'ru',
                        'clientOptions' => [
                            'relative_urls' => false,
                            'plugins' => [
                                'textcolor colorpicker',
                                "advlist autolink lists link charmap print preview anchor",
                                "searchreplace visualblocks code fullscreen",
                                "insertdatetime media table contextmenu paste",
                                'image'
                            ],
                            'toolbar' => Yii::$app->params['toolbar'],
                        ]
                    ])->label('Doses')
                    ?>

                    <!-- SEO FIELDS -->
                    <hr>
                    <h2>SEO options</h2>
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
                    ])->label('SEO title')
                    ?>
                    <?= $form->field($products_translation, 'seoDescription', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('SEO description')
                    ?>
                    <?= $form->field($products_translation, 'seoKeywords', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('SEO keywords')
                    ?>

                    <? if (!$product->isNewRecord): ?>
                        <!--PARAMS-->
                        <hr>
                        <h2>Params</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php if (!empty($product->params)) : ?>

                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-4 text-center">
                                                        Name
                                                    </th>
                                                    <th class="col-lg-5 text-center">
                                                        Value
                                                    </th>
                                                    <th class="col-lg-2 text-center">
                                                        Languages
                                                    </th>
                                                    <th class="col-lg-1 text-center">
                                                        Control
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($product->params as $param) : ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?= $param->translation->name ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= $param->translation->value ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <? if (count($languages) > 1): ?>
                                                                <? $translations = ArrayHelper::index($param->translations, 'language_id') ?>
                                                                <? foreach ($languages as $language): ?>
                                                                    <a href="<?= Url::to([
                                                                        'add-param',
                                                                        'id' => $param->id,
                                                                        'languageId' => $language->id
                                                                    ]) ?>"
                                                                       type="button"
                                                                       class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                                                       ?> btn-xs"><?= $language->name ?></a>
                                                                <? endforeach; ?>
                                                            <? endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= Url::to([
                                                                'add-param',
                                                                'id' => $param->translation->param_id,
                                                                'languageId' => $param->translation->language_id
                                                            ]) ?>"
                                                               class="btn btn-primary pull-left">E</a>
                                                            <a href="<?= Url::to([
                                                                'delete-param',
                                                                'id' => $param->translation->param_id,
                                                                'productId' => $param->product_id,
                                                                'languageId' => $selectedLanguage
                                                            ]) ?>"
                                                               class="btn btn-danger pull-right">D</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <? endif; ?>
                                        <a href="<?= Url::to([
                                            'add-param',
                                            'productId' => $product->id,
                                            'languageId' => $product->translation->language_id
                                        ]) ?>"
                                           class="btn btn-primary pull-right">Add param</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>

                    <input type="submit" class="btn btn-primary pull-right" value="<?= 'Save' ?>">
                </div>
            </div>
        </div>
    </div>
<? ActiveForm::end(); ?>

<!--PRODUCT PRICES-->
<? if (!$product->isNewRecord): ?>
    <?= $this->render('/price/add', [
        'priceList' => $product->prices,
        'priceModel' => new ProductPrice(),
        'priceTranslationModel' => new ProductPriceTranslation(),
        'product' => $product,
        'languages' => $languages,
        'selectedLanguage' => $selectedLanguage
    ]) ?>
<? endif; ?>