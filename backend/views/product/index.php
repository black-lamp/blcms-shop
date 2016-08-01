<?php
use bl\cms\shop\backend\assets\PjaxLoaderAsset;
use  bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $categories CategoryTranslation
 * @var $languages Language[]
 */

$this->title = \Yii::t('shop', 'Product list');

PjaxLoaderAsset::register($this);
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= \Yii::t('shop', 'Product list'); ?>
            </div>
            <div class="panel-body">
                <? Pjax::begin([
                    'linkSelector' => '.product-nav',
                    'enablePushState' => false,
                    'timeout' => 10000,
                ]) ?>
                <table class="table table-hover">
                    <? if (!empty($products)): ?>
                        <thead>
                        <tr>
                            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Position'); ?></th>
                            <th class="col-md-4 text-center"><?= \Yii::t('shop', 'Title'); ?></th>
                            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Category'); ?></th>
                            <? if(count($languages) > 1): ?>
                                <th class="col-lg-2 text-center"><?= \Yii::t('shop', 'Language'); ?></th>
                            <? endif; ?>
                            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Edit'); ?></th>
                            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Delete'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($products as $product): ?>
                            <tr class="text-center">
                                <td class="text-center">
                                    <?= $product->position ?>
                                    <a href="<?= Url::to([
                                        'up',
                                        'id' => $product->id
                                    ]) ?>" class="product-nav glyphicon glyphicon-arrow-up text-primary pull-left">
                                    </a>
                                    <a href="<?= Url::to([
                                        'down',
                                        'id' => $product->id
                                    ]) ?>" class="product-nav glyphicon glyphicon-arrow-down text-primary pull-left">
                                    </a>
                                </td>
                                <td class="text-left">
                                    <?= $product->translation->title ?>
                                </td>
                                <td>
                                    <? if(!empty($product->category)): ?>
                                        <?= $product->category->translation->title ?>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? if(count($languages) > 1): ?>
                                        <? $translations = ArrayHelper::index($product->translations, 'language_id') ?>
                                        <? foreach ($languages as $language): ?>
                                            <a href="<?= Url::to([
                                                'save',
                                                'productId' => $product->id,
                                                'languageId' => $language->id
                                            ]) ?>"
                                               type="button"
                                               class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                               ?> btn-xs"><?= $language->name ?></a>
                                        <? endforeach; ?>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <a href="<?= Url::to([
                                        'save',
                                        'productId' => $product->id,
                                        'languageId' => $product->translation->language->id
                                    ])?>" class="glyphicon glyphicon-edit text-warning btn btn-default btn-sm">
                                    </a>
                                </td>

                                <td>
                                    <a href="<?= Url::to([
                                        'remove',
                                        'id' => $product->id
                                    ])?>" id="remove" class="product-nav glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    <? endif; ?>
                </table>
                <? Pjax::end() ?>

                <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>