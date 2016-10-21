<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\frontend\assets\CartAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;

CartAsset::register($this);
?>

<h1 class="text-center"><?= \Yii::t('shop', 'Your cart is empty.'); ?></h1>

<div class="empty-cart">
    <?= Html::a(\Yii::t('shop', 'Go to shop'), Url::toRoute('/shop'), ['class' => 'btn btn-primary text-center']); ?>
    <div>
        <?= Html::img('/images/empty-cart-image.png'); ?>
    </div>
</div>
