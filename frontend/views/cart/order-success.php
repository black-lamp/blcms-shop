<?php

use yii\helpers\Url;

$this->title = 'Замовлення';

?>

<div class="row">
    <div class="col-md-12">
        <h4>
            <?= Yii::t('frontend/shop/product', 'Спасибо за заказ'); ?>
        </h4>
        <p>
            <?= Yii::t('frontend/shop/product', 'Наши менеджеры свяжутся с вами в самое ближайшее время'); ?>
        </p>
        <p></p>
        <a href="<?= Url::to(['/shop']) ?>" class="btn btn-primary">
            <?= Yii::t('frontend/shop/product', 'Вернуться к просмотру магазина'); ?>
        </a>
    </div>
</div>
