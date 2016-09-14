<?php
use bl\cms\shop\frontend\models\Cart;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use bl\cms\shop\common\entities\Clients;
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var Cart $cart
 * @var Clients $client
 */
$this->title = 'Корзина';
$client = new Clients();
?>
    <h1><?= Yii::t('frontend/shop/cart', 'Корзина'); ?></h1>


<?php if(!empty($cart->items)) : ?>

    <!-- CLEAR CART -->
    <a href="<?= Url::to(['/shop/cart/clear']) ?>" class="pull-right btn btn-primary clear-btn">
        <?= Yii::t('frontend/shop/cart', 'Очистить корзину'); ?>
    </a>

    <!-- CART ITEMS -->
    <div class="cart">
        <?php foreach ($cart->items as $key=>$item): ?>
            <div class="item">
                <span class="cart-item-number"><?= $key + 1 ?></span>
                <div class="cart-image">
                    <?= Html::img($item->price->product->thumbImage, [
                        'itemprop' => "image",
                        'class' => 'cart-image'
                    ]) ?>
                </div>
                <div class="cart-item-info">
                    <h2>
                        <a href="<?= Url::toRoute(['/shop/product/show', 'id' => $item->price->product_id]);?>">
                            <?= $item->price->product->translation->title ?>
                        </a>
                    </h2>

                    <div class="values">
                        <div>
                            <p class="label"><?= Yii::t('frontend/shop/cart', 'Тип'); ?>:</p>
                            <p class="value"><?= $item->price->translation->title; ?></p>
                        </div>
                        <div>
                            <p class="label"><?= Yii::t('frontend/shop/cart', 'Количество'); ?>:</p>
                            <p class="value"><?= $item->count; ?></p>
                        </div>
                        <div>
                            <p class="label"><?= Yii::t('frontend/shop/cart', 'Цена'); ?>:</p>
                            <p class="value"><?= $item->price->currencySalePrice; ?></p>
                        </div>
                        <div>
                            <p class="label"><?= Yii::t('frontend/shop/cart', 'Сумма'); ?>:</p>
                            <p class="value"><?= $item->price->currencySalePrice * $item->count; ?></p>
                        </div>
                    </div>
                </div>


                <div class="price">
                    <p class="new-price">
                        <?= $item->price->currencySalePrice; ?> грн
                    </p>
                    <p class="old-price">
                        <?= $item->price->currencyPrice; ?> грн
                    </p>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

    <!-- SUM -->
    <p class="total-sum"><?= Yii::t('frontend/shop/cart', 'Итого'); ?>: <?= $cart->sum; ?> грн</p>

    <!--ORDER FORM-->
    <div class="col-md-12 text-center">
        <a href="#" class="btn btn-primary btn-lg" id="order-btn">
            <?= Yii::t('frontend/shop/cart', 'Оформить заказ'); ?>
        </a>
    </div>
    <div class="row" id="order-form" style="display:none;">
        <div class="col-md-12">

            <div class="col-md-6">

                <?php $createOrder = ActiveForm::begin([
                    'action' => Url::to(['']),
                    'method' => 'post',
                ]) ?>

                <div class="form-group">
                    <?= $createOrder->field($client, 'name', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label(Yii::t('frontend/shop/order', 'Имя')) ?>
                </div>

                <div class="form-group">
                    <?= $createOrder->field($client, 'email', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Email') ?>
                </div>

                <div class="form-group">
                    <?= $createOrder->field($client, 'phone', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Телефон') ?>
                </div>

                <?= Html::submitButton(Yii::t('frontend/shop/order', 'Отправить'), ['class' => 'btn btn-primary pull-right']) ?>

                <?php ActiveForm::end() ?>

            </div>

        </div>
    </div>

    <!-- IF CART IS EMPTY -->
<?php else : ?>
    <p>
        <?= Yii::t('frontend/shop/cart', 'У Вас пока ещё нет товаров'); ?>.
        <br>
        <?= Yii::t('frontend/shop/cart', 'Вернитесь в'); ?> <a href="<?= Url::to(['/shop/category/show']) ?>"><?= Yii::t('frontend/shop/cart', 'магазин'); ?></a>, <?= Yii::t('frontend/shop/cart', 'чтобы выбрать товар'); ?>.
    </p>
<?php endif ?>