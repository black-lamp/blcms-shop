<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $menuItems Category
 * @var $category Category
 * @var $products Product
 */

use bl\multilang\entities\Language;
use bl\cms\multishop\common\entities\Category;
use bl\cms\multishop\common\entities\Product;
use yii\helpers\Url;


$this->title = 'Интернет-магазин';
?>

<div class="row products">
    <div class="col-md-12">
        <div class="col-md-3 menu-categories">
            <?php foreach ($menuItems as $menuItem) : ?>
                <p>
                    <a href="<?= Url::toRoute(['category/show', 'categoryId' => $menuItem->id]); ?>" >
                        <?= $menuItem->translation->title ?>
                    </a>
                </p>
            <? endforeach; ?>
        </div>
        <div class="col-md-9">
            <h1><?=$category->translation->title; ?></h1>
            <? foreach ($products as $product): ?>
                <div class="col-md-4 text-center product">
                    <a href="<?= Url::to(['product/show', 'id' => $product->id])?>">
                        <div class="img">
                            <img src="/admin/upload/gallery/154.jpg" alt="" width="">
                        </div>
                        <div class="content">
                            <div class="cell">
                                <span class="title">
                                    <?= $product->translation->title ?>
                                </span>
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

