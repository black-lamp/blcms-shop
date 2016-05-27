<?php
/**
 * Created by Albert Gainutdinov
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Кошик';

?>

<h1 class="pull-left"><?= Yii::t('frontend/shop/cart', 'Корзина'); ?></h1>

<? if(!empty($products)) : ?>
    <a href="<?= Url::to(['/shop/cart/clear']) ?>" class="pull-right btn btn-primary clear-btn">
        <?= Yii::t('frontend/shop/cart', 'Очистить корзину'); ?>
    </a>
<? endif ?>



<div class="cart">
    <? if(!empty($products)) : ?>
        <? foreach($products as $key => $product) : ?>
            <div class="item">

                <span>
                    <?= $key + 1 ?>
                </span>

                <h4>
                    <a href="<?= Url::toRoute(['shop/product/' . $product['id']]) ?>">
                        <?= $product['title'] ?>
                    </a>
                </h4>

                <div class="content">

                    <div class="params">

                        <div class="tara">
                            <span>
                                <?= $product['tara'] ?> x <?=$product['count'];?>
                            </span>
                        </div>

                        <div class="price">
                            <span>
                                <?= $product['price'] ?> грн.
                            </span>
                        </div>
                    </div>

                    <div class="image">
                        <img src="/admin/upload/shop-images/<?= $product['imageFile'] ?>" alt="">
                    </div>

                </div>

            </div>
        <? endforeach ?>

    <p class="total-sum">Итого: <?= $totalSum; ?></p>
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="#" class="btn btn-primary btn-lg" id="order-btn">
                <?= Yii::t('frontend/shop/cart', 'Оформить заказ'); ?>
            </a>
        </div>
    </div>

    <div class="row" id="order-form" style="display:none;">
        <div class="col-md-12">

            <div class="col-md-6">

                <? $createOrder = ActiveForm::begin([
                    'action' => Url::to(['']),
                    'method' => 'post',
                ]) ?>

                <div class="form-group">
                    <?= $createOrder->field($order, 'name', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label(Yii::t('frontend/shop/order', 'Имя')) ?>
                </div>

                <div class="form-group">
                    <?= $createOrder->field($order, 'email', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Email') ?>
                </div>

                <div class="form-group">
                    <?= $createOrder->field($order, 'phone', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Телефон') ?>
                </div>

                <?= Html::submitButton(Yii::t('frontend/shop/order', 'Отправить'), ['class' => 'btn btn-primary pull-right']) ?>

                <? ActiveForm::end() ?>

            </div>

        </div>
    </div>
</div>



<? else : ?>

    <p>
        <?= Yii::t('frontend/shop/cart', 'У Вас пока ещё нет товаров'); ?>.
        <br>
        <?= Yii::t('frontend/shop/cart', 'Вернитесь в'); ?> <a href="<?= Url::to(['/shop']) ?>"><?= Yii::t('frontend/shop/cart', 'магазин'); ?></a>, <?= Yii::t('frontend/shop/cart', 'чтобы выбрать товар'); ?>.
    </p>

<? endif ?>