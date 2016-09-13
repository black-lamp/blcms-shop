<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
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
?>
    <div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
        <? echo Breadcrumbs::widget([
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
        <div class="col-md-6 image">
            <?= Html::img('', [
                'itemprop' => "image"
            ]) ?>
            <div class="row text-block">
                <p>
                    <?= Yii::t('frontend/shop/product', 'Для получения быстрой консультации звоните продавцу на мобильный:'); ?>
                </p>
                <p>
                    <span class="fa fa-phone-square"></span>
                    (050) 599 45 92
                </p>
                <p>
                    <span class="fa fa-phone-square"></span>
                    (068) 303 14 82
                </p>
                <p>
                    <span class="fa fa-phone-square"></span>
                    (044) 258 96 11
                </p>

                <p>
                    <a class="call" href="tel: +380683031482">
                        <?= Yii::t('frontend/shop/product', 'Позвонить'); ?>
                    </a>
                </p>
                <p>
                    <?= \Yii::t('frontend/shop/product', 'Товар указанный на сайте всегда в наличии. Возможна отгрузка со склада в Киеве или доставка "Новой Почтой" по Украине. Оплату доставки оплачивает Покупатель <a href="https://novaposhta.ua/privatnim_klientam/ceny_i_tarify" rel="nofollow">согласно тарифам перевозчика</a>. Условия и порядок обмена или возврата Товара определяются действующим законодательством Украины.'); ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <h1 itemprop="name"><?= $product->translation->title ?></h1>

            <!-- DESCRIPTION -->
            <? if (!empty($product->translation->description)) : ?>
                <div class="intro-text">
                    <strong><?= $product->translation->description ?></strong>
                </div>
            <? endif ?>


            <!--COUNTRY-->
            <? if (!empty($country->translation->title)) : ?>
                <div class="dose">
                    <h4 class="small"><?= Yii::t('frontend/shop/product', 'Страна производитель'); ?></h4>
                    <?= $country->translation->title ?>
                </div>
            <? endif; ?>


            <? $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/shop/cart/add-to-cart']
            ]) ?>
            <div class="price-wrap">


<!--                <!-- PRICES -->-->
<!--                --><?// if (!empty($product->prices)) : ?>
<!--                    <table class="table table-bordered price-table">-->
<!--                        --><?// foreach ($product->prices as $key => $price) : ?>
<!--                            <tr>-->
<!--                                <td>-->
<!--                                    <input type="radio" id="addtocartmodel-price_id" name="AddToCartModel[price_id]"-->
<!--                                           value="--><?//= $price->id; ?><!--" --><?//= ($key == 0) ? 'checked' : ''; ?><!-->-->
<!--                                </td>-->
<!--                                <td>-->
<!--                                    --><?//= $price->translation->title ?>
<!--                                </td>-->
<!--                                <td>-->
<!--                                    --><?// if (!empty($price->price)) : ?>
<!--                                        <strong>-->
<!--                                            <span>--><?//= $price->currencySalePrice ?><!--</span> грн.-->
<!---->
<!--                                        </strong>-->
<!--                                        --><?// if (!empty($price->sale)) : ?>
<!--                                            <strike>-->
<!--                                                <sup>-->
<!--                                                    <span-->
<!--                                                        class="sup">--><?//= $price->currencyPrice ?><!--</span>-->
<!--                                                    грн.-->
<!--                                                </sup>-->
<!--                                            </strike>-->
<!--                                        --><?// endif ?>
<!--                                    --><?// endif ?>
<!--                                </td>-->
<!--                            </tr>-->
<!--                        --><?// endforeach ?>
<!--                    </table>-->
<!--                --><?// endif ?>

                <!-- QUANTITY -->
<!--                <div class="count">-->
<!--                    --><?//= $form->field($addToCart, 'count')->textInput([
//                        'data-action' => 'text',
//                        'class' => 'form-control',
//                        'id' => 'count'
//                    ])->label(Yii::t('frontend/shop/product', 'Количество')) ?>
<!--                </div>-->
<!--                --><?// if (!empty($product->prices[0]->currencyPrice)) : ?>
<!--                    <div class="product-price pull-right" itemprop="offers" itemscope-->
<!--                         itemtype="http://schema.org/Offer">-->
<!--                        <link itemprop="availability" href="http://schema.org/InStock"/>-->
<!--                        <meta itemprop="priceCurrency" content="UAH"/>-->
<!--                        <span class="price-elem" itemprop="price">-->
<!--                        --><?//= $product->prices[0]->currencySalePrice ?>
<!--                    </span> грн-->
<!--                    </div>-->
<!--                --><?// endif ?>
            </div>

                <!--FOR ADDING USE SCRIPT /frontend/web/js/script.js -->
                <input type="submit" value="<?= Yii::t('frontend/shop/product', 'Добавить в корзину') ?>"
                       class="add-to-cart-button">

            <? $form->end() ?>

            <!--FULL TEXT -->
            <? if (!empty($product->translation->full_text)) : ?>
                <div class="full-text" itemprop="description">
                    <h4 class="small"><?= Yii::t('frontend/shop/product', 'Описание'); ?></h4>
                    <?= $product->translation->full_text ?>
                </div>
            <? endif ?>
        </div>
    </div>

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
                            <? if (!empty($recommendedProduct->prices[0]->price)) : ?>
                                <span class="new"><?= $recommendedProduct->prices[0]->currencySalePrice ?> грн.</span>
                            <? endif ?>

                                &nbsp;

                                <? if (!empty($recommendedProduct->prices[0]->sale)) : ?>
                                    <strike class="text-muted"><?= $recommendedProduct->prices[0]->currencyPrice ?>
                                        грн.</strike>
                                <? endif ?>
                        </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>