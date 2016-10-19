<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;use yii\helpers\Html;
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
//ProductAsset::register($this);
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
    <?php if(Yii::$app->session->hasFlash('alert')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('alert') ?>
        </div>
    <?php endif; ?>
</div>

<!--PRODUCT CARD-->
<div class="row product-page">

    <div class="col-md-6">
        <h1><?= $product->translation->title ?></h1>

        <!-- DESCRIPTION -->
        <?php if (!empty($product->translation->description)) : ?>
            <div class="intro-text">
                <strong><?= $product->translation->description ?></strong>
            </div>
        <?php endif ?>


        <!--COUNTRY-->
        <?php if (!empty($country)) : ?>
            <div class="dose">
                <h4 class="small"><?= Yii::t('frontend/shop/product', 'Страна производитель'); ?></h4>
                <?= $country->id; ?>
            </div>
        <?php endif; ?>


        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/shop/cart/add']
        ]); ?>

        <div class="price-wrap">
            <!-- PRICES -->
            <?php if (!empty($product->prices)) : ?>

                <?= $form->field($cart, 'priceId')->radioList(ArrayHelper::map($product->prices, 'id', function($model) {
                    return $model->translation->title . \Yii::$app->formatter->asCurrency($model->salePrice) . Html::tag('strike', \Yii::$app->formatter->asCurrency($model->price));
                })); ?>
            <?php endif ?>

            <!-- QUANTITY -->
            <div class="count">
                <?= $form->field($cart, 'count')->textInput([
                    'type' => 'number',
                    'min' => '1',
                    'value' => '1',
                    'data-action' => 'text',
                    'class' => 'form-control',
                    'id' => 'count'
                ])->label(Yii::t('frontend/shop/product', 'Количество')) ?>
                <?= $form->field($cart, 'productId')->hiddenInput(['value' => $product->id]); ?>
            </div>
            <?php if (!empty($product->prices[0]->price)) : ?>
                <div class="product-price pull-right">
                    <span class="price-elem">
                        <?= $product->prices[0]->salePrice ?>
                    </span> грн
                </div>
            <?php endif ?>


            <!--ADD TO CART-->
            <input type="submit" value="<?= Yii::t('shop', 'Add to cart') ?>"
                   class="add-to-cart-button" id="cart_btn" data-id="<?=$product->id; ?>">

        <?php $form->end() ?>

        <!--FULL TEXT -->
        <?php if (!empty($product->translation->full_text)) : ?>
            <div class="full-text">
                <h4 class="small"><?= Yii::t('shop', 'Description'); ?></h4>
                <?= $product->translation->full_text ?>
            </div>
        <?php endif ?>
    </div>

        <!--IMAGE-->
        <?php if (!empty($product->images)) : ?>
            <div class="col-md-6">
                <?= Html::img($product->image->small, [
                    'class' => 'media-object img-responsive',
                    'alt' => Html::encode($product->image->alt)
                ]); ?>
            </div>
        <?php endif; ?>
</div>

