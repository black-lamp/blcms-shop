<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\frontend\assets\ProductAsset;
use bl\cms\shop\frontend\models\AddToCartModel;
use yii\bootstrap\ActiveForm;
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
$addToCart = new AddToCartModel();
ProductAsset::register($this);
?>
    <div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
        <?php echo Breadcrumbs::widget([
            'itemTemplate' => "<li><b><span itemprop=\"title\">{link}</span></b></li>\n",
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

    <div class="row product-page" itemscope itemtype="http://schema.org/Product">

        <div class="col-md-6">
            <h1 itemprop="name"><?= $product->translation->title ?></h1>

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
                'action' => ['/shop/cart/add-to-cart']
            ]) ?>
            <div class="price-wrap">


                <!-- PRICES -->
                <?php if (!empty($product->prices)) : ?>
                    <table class="table table-bordered price-table">
                        <?php foreach ($product->prices as $key => $price) : ?>
                            <tr>
                                <td>
                                    <input type="radio" id="addtocartmodel-price_id" name="AddToCartModel[price_id]"
                                           value="<?= $price->id; ?>" <?= ($key == 0) ? 'checked' : ''; ?>>
                                </td>
                                <td>
                                    <?= $price->translation->title ?>
                                </td>
                                <td>
                                    <?php if (!empty($price->price)) : ?>
                                        <strong>
                                            <span><?= \Yii::$app->formatter->asCurrency($price->salePrice); ?></span> грн.

                                        </strong>
                                        <?php if (!empty($price->sale)) : ?>
                                            <strike>
                                                <sup>
                                                    <span
                                                        class="sup"><?= \Yii::$app->formatter->asCurrency($price->price); ?></span>
                                                    грн.
                                                </sup>
                                            </strike>
                                        <?php endif ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                <?php endif ?>

                <!-- QUANTITY -->
                <div class="count">
                    <?= $form->field($addToCart, 'count')->textInput([
                        'data-action' => 'text',
                        'class' => 'form-control',
                        'id' => 'count'
                    ])->label(Yii::t('frontend/shop/product', 'Количество')) ?>
                </div>
                <?php if (!empty($product->prices[0]->price)) : ?>
                    <div class="product-price pull-right" itemprop="offers" itemscope
                         itemtype="http://schema.org/Offer">
                        <link itemprop="availability" href="http://schema.org/InStock"/>
                        <meta itemprop="priceCurrency" content="UAH"/>
                        <span class="price-elem" itemprop="price">
                            <?= $product->prices[0]->salePrice ?>
                        </span> грн
                    </div>
                <?php endif ?>



                <!--ADD TO CART-->
                <!--FOR ADDING USE SCRIPT web/script/script.js -->
                <input type="submit" value="<?= Yii::t('frontend/shop/product', 'Добавить в корзину') ?>"
                       class="add-to-cart-button" id="cart_btn" data-id="<?=$product->id; ?>">

            <?php $form->end() ?>

            <!--FULL TEXT -->
            <?php if (!empty($product->translation->full_text)) : ?>
                <div class="full-text" itemprop="description">
                    <h4 class="small"><?= Yii::t('frontend/shop/product', 'Описание'); ?></h4>
                    <?= $product->translation->full_text ?>
                </div>
            <?php endif ?>
        </div>

    </div>

    <!--IMAGE-->
    <?php if (!empty($product->images)) : ?>
        <div class="col-md-6">
            <?= Html::img(ProductImage::getThumb($product->images[0]->file_name)); ?>
        </div>
    <?php endif; ?>

    <!--RECOMMENDED PRODUCTS-->
<?php if (!empty($recommendedProducts)) : ?>
    <div class="row products recommended">
        <h4><?= Yii::t('frontend/shop/product', 'Рекомендуемые товары') ?></h4>
        <?php foreach ($recommendedProducts as $recommendedProduct) : ?>
            <div class="text-center product">
                <a href="<?= Url::to(['product/show', 'id' => $recommendedProduct->id]) ?>">
                    <div class="img">
                        <?= Html::img($recommendedProduct->thumbImage) ?>
                    </div>
                    <div class="content">
                        <div class="cell">
                        <span class="title">
                            <?= !empty($recommendedProduct->translation->anchor_name) ? $recommendedProduct->translation->anchor_name : $recommendedProduct->translation->title; ?>
                        </span>
                            <span class="price">
                            <?php if (!empty($recommendedProduct->prices[0]->price)) : ?>
                                <span class="new"><?= $recommendedProduct->prices[0]->currencySalePrice ?> грн.</span>
                            <?php endif ?>

                                <?php if (!empty($recommendedProduct->prices[0]->sale)) : ?>
                                    <strike class="text-muted"><?= $recommendedProduct->prices[0]->currencyPrice ?>
                                        грн.</strike>
                                <?php endif ?>
                        </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>