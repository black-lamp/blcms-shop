<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\frontend\assets\ProductAsset;
use bl\cms\shop\widgets\assets\RecommendedProductsAsset;
use bl\cms\shop\widgets\RecommendedProducts;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $cart \bl\cms\cart\models\CartForm
 */

RecommendedProductsAsset::register($this);
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
                'label' => (!empty($product->category->translation->title)) ? $product->category->translation->title : '',
                'url' => (!empty($product->category)) ? Url::toRoute(['category/show', 'id' => $product->category->id]) : '',
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
<div class="product-page">

    <!--TITLE-->
    <h1 class="col-md-12 text-center"><?= (!empty($product->translation->title))
            ? $product->translation->title : '' ?>
    </h1>

    <div class="row">
        <!--IMAGE-->
        <div class="col-md-4 image">
            <?= Html::img(
                (!empty($product->images)) ? $product->image->thumb : Url::toRoute('/images/default.jpg'),
                [
                    'class' => 'media-object img-responsive',
                    'alt' => (!empty($product->images)) ?
                        Html::encode($product->image->alt) :
                        ''
                ]); ?>
        </div>


        <div class="col-md-8">
            <!--ARTICULUS-->
            <?php if (!empty($product->articulus)) : ?>
                <div class="intro-text">
                    <p>
                        <strong><?= \Yii::t('shop', 'SKU'); ?></strong>: <?= $product->articulus; ?>
                    </p>
                </div>
            <?php endif ?>

            <!--VENDOR-->
            <?php if (!empty($product->vendor)) : ?>
                <div class="intro-text">
                    <p>
                        <strong><?= \Yii::t('shop', 'Vendor'); ?></strong>: <?= $product->vendor->title; ?>
                    </p>
                </div>
            <?php endif ?>

            <!--COUNTRY-->
            <?php if (!empty($product->productCountry)) : ?>
                <p>
                    <strong><?= \Yii::t('shop', 'Country'); ?></strong>: <?= $product->productCountry->translation->title; ?>
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
        </div>
    </div>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/cart/cart/add']
        ]); ?>

        <!--PRICES-->
        <?php if (!empty($product->prices)) : ?>
            <?= $form->field($cart, 'priceId', ['options' => ['class' => 'col-md-3']])
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
        <?= $form->field($cart, 'count', ['options' => ['class' => 'col-md-3']])->
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

        <!--SUBMIT BUTTON-->
        <?= Html::submitButton(Yii::t('shop', 'Add to cart'),
            [
                'class' => 'btn btn-primary'
            ]); ?>

        <!--ADD TO FAVORITE-->
        <?php if (!Yii::$app->user->isGuest) : ?>
            <?php if (!$product->isFavorite()) : ?>
                <?= Html::a(
                    Yii::t('shop', 'Add to favorites'),
                    Url::to(['/shop/favorite-product/add', 'productId' => $product->id]),
                    ['class' => 'btn btn-info']
                ); ?>
            <?php else : ?>
                <?= Html::a(
                    Yii::t('shop', 'Remove from favorites'),
                    Url::to(['/shop/favorite-product/remove', 'productId' => $product->id]),
                    ['class' => 'btn btn-warning']
                ); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php $form::end(); ?>
    </div>

    <!--FULL TEXT -->
    <?php if (!empty($product->translation->full_text)) : ?>
        <div class="full-text">
            <?= $product->translation->full_text ?>
        </div>
    <?php endif ?>

    <?php if (!empty($product->params)) : ?>
        <h4 class="text-center"><?= Yii::t('shop', 'Params'); ?>:</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                <?php foreach ($product->params as $param): ?>
                    <tr>
                        <td class="text-right text-uppercase col-md-4"><?= $param->translation->name; ?></td>
                        <td class="col-md-8"><?= $param->translation->value; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<!--RECOMMENDED PRODUCTS-->
<div class="row">
    <?= RecommendedProducts::widget([
        'id' => $product->id,
    ]); ?>
</div>