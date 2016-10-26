<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\frontend\assets\ProductAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $categories Category
 * @var $category Category
 * @var $country ProductCountry
 * @var $params Param
 * @var $recommendedProducts Product
 */

ProductAsset::register($this);
?>

<!--BREADCRUMBS-->
<div>
    <?php echo Breadcrumbs::widget([
        'itemTemplate' => "<li><b><span>{link}</span></b></li>\n",
        'homeLink' => [
            'label' => Yii::t('frontend/navigation', 'Главная'),
            'url' => Url::toRoute(['/']),
            'itemprop' => 'url',
        ],
        'links' => [
            [
                'label' => Yii::t('frontend/navigation', 'Магазин'),
                'url' => Url::toRoute(['/shop']),
                'itemprop' => 'url',
            ],
            [
                'label' => $category->translation->title,
                'url' => Url::toRoute(['category/show', 'id' => $category->id]),
                'itemprop' => 'url',
            ],
            $product->translation->title,
        ],
    ]);
    ?>
</div>

<!--ALERT-->
<div class="col-lg-12">
    <?php if (Yii::$app->session->hasFlash('alert')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('alert') ?>
        </div>
    <?php endif; ?>
</div>

<!--PRODUCT CARD-->
<div class="row product-page">

    <h1 class="col-md-12 text-center"><?= $product->translation->title ?></h1>

    <div class="col-md-4 image">
        <!--IMAGE-->
            <?= Html::img(
                (!empty($product->images)) ? $product->image->thumb : Url::toRoute('/images/default.jpg'),
                [
                'class' => 'media-object img-responsive',
                'alt' => (!empty($product->images)) ? Html::encode($product->image->alt) : $product->translation->title
            ]); ?>

    </div>


    <div class="col-md-8">
        <!--ARTICULUS-->
        <?php if (!empty($product->articulus)) : ?>
            <div class="intro-text">
                <p>
                    <strong><?=\Yii::t('shop', 'SKU'); ?></strong>: <?= $product->articulus; ?>
                </p>
            </div>
        <?php endif ?>

        <!--VENDOR-->
        <?php if (!empty($product->vendor)) : ?>
            <div class="intro-text">
                <p>
                    <strong><?=\Yii::t('shop', 'Vendor'); ?></strong>: <?= $product->vendor->title; ?>
                </p>
            </div>
        <?php endif ?>

        <!--COUNTRY-->
        <?php if (!empty($product->productCountry)) : ?>
            <p>
                <strong><?=\Yii::t('shop', 'Country'); ?></strong>: <?= $product->productCountry->translation->title; ?>
            </p>
        <?php endif; ?>

        <!--AVAILABILITY-->
        <?php if (!empty($product->availability)) : ?>
            <div class="availability">
                <p class="">
                    <strong><?= $product->productAvailability->translation->title; ?></strong>
                </p>
                <p>
                    <?= $product->productAvailability->translation->description; ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- DESCRIPTION -->
        <p class="article-label"><?= Yii::t('shop', 'Description'); ?></p>
        <?php if (!empty($product->translation->description)) : ?>
            <div class="description">
                <?= $product->translation->description ?>
            </div>
        <?php endif ?>

        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/shop/cart/add']
        ]); ?>

        <!--PRICES-->
        <?php if (!empty($product->prices)) : ?>
            <?= $form->field($cart, 'priceId', ['options' => ['class' => 'col-md-4']])
                ->dropDownList(ArrayHelper::map($product->prices, 'id',
                    function ($model) {
                        $priceItem = $model->translation->title . ' - ' . \Yii::$app->formatter->asCurrency($model->salePrice);
                        return $priceItem;
                }))
                ->label(\Yii::t('shop', 'Price'));
            ?>
        <?php elseif (!empty($product->price)) : ?>
            <?= \Yii::$app->formatter->asCurrency($product->price); ?>
        <?php endif; ?>

        <!--QUANTITY-->
        <?= $form->field($cart, 'count', ['options' => ['class' => 'col-md-4']])->
        textInput([
            'type' => 'number',
            'min' => '1',
            'value' => '1',
            'data-action' => 'text',
            'class' => 'form-control',
            'id' => 'count'
        ])->label(\Yii::t('shop', 'Count'));
        ?>
        <!--PRODUCT ID HIDDEN INPUT-->
        <?= $form->field($cart, 'productId')->hiddenInput(['value' => $product->id])->label(false); ?>

        <?= Html::submitButton(Yii::t('shop', 'Add to cart'),
            [
                'class' => 'btn btn-primary'
            ]); ?>

        <?php $form::end(); ?>

    </div>

    <!--FULL TEXT -->
    <?php if (!empty($product->translation->full_text)) : ?>
        <div class="full-text">
            <?= $product->translation->full_text ?>
        </div>
    <?php endif ?>
</div>

<?= \bl\cms\shop\widgets\RecommendedProducts::widget([
    'id' => $product->id
]); ?>