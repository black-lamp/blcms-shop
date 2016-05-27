<?php
use bl\multilang\entities\Language;
use common\modules\multishop\common\entities\Product;
use yii\helpers\Url;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $categories Category
 */
?>

<div class="row products">
    <div class="col-md-12">
        <div class="col-md-3 menu-categories">
            <?php foreach ($categories as $category) : ?>
                <p>
                    <a href="<?= Url::toRoute(['category/show', 'categoryId' => $category->id]); ?>" >
                        <?= $category->translation->title ?>
                    </a>
                </p>
            <? endforeach; ?>
        </div>
        <div class="col-md-9">
            <h1><?= $product->translation->title; ?></h1>

            <p>
                <?= $product->translation->description; ?>
            </p>

        </div>
    </div>
</div>
