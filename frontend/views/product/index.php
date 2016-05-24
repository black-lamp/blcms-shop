<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * Date: 24.05.2016
 * Time: 11:14
 *
 *  * @var $categories Categories
 *  * @var $products Product
 */

use yii\helpers\Url;

$this->title = 'Multishop all products';
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
            <h1>Магазин</h1>
            <?php foreach ($products as $product) : ?>
                <div class="col-md-4 text-center product">
                    <a href="<?= Url::toRoute(['product/show', 'id' => $product->id]); ?>">
                        <div class="img">
                                <img src="/admin/upload/gallery/154.jpg" alt="">
                        </div>
                        <div class="content">
                            <div class="cell">
                                <span class="title"><?= $product->translation->title; ?></span>
                                <span class="price">
                                    <span class="new">
                                        <?= $product->price; ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>
