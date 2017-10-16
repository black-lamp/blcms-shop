<?php
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 * @var ProductPrice[] $prices
 */
use bl\cms\shop\common\entities\ProductPrice;

?>

<?php if(!empty($prices)): ?>
    <div class="prices">
        <?php foreach ($prices as $price): ?>
            <div class="price-row _group-<?= $price->user_group_id ?> <?= (Yii::$app->user->identity->user_group_id == $price->user_group_id) || (Yii::$app->user->isGuest && $price->user_group_id == 1) ? 'active' : '' ?>">
                <div class="group-title">
                    <?= $price->userGroup->translation->title ?>:
                </div>
                <strong class="price text-success product-discount-price">
                    <?= Yii::$app->formatter->asCurrency($price->price->discountPrice) ?><br>
                </strong>
                <strike class="old-price text-danger product-price" style="display: <?= empty($price->price->discount_type_id) ? 'none' : 'block' ?>">
                    <?= Yii::$app->formatter->asCurrency($price->price->oldPrice) ?>
                </strike>
                <div class="clearfix"></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
