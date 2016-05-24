<?php
use \common\modules\multishop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $categories CategoryTranslation */
/* @var $languages Language[] */

$this->title = 'Products category list';

?>

<?php

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Products list' ?>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <? if (!empty($products)): ?>
                        <thead>
                        <tr>
                            <th class="col-lg-3"><?= 'Title' ?></th>
                            <th class="col-lg-3"><?= 'Category' ?></th>
                            <th class="col-lg-3"><?= 'Description' ?></th>
                            <? if(count($languages) > 1): ?>
                                <th class="col-lg-3"><?= 'Language' ?></th>
                            <? endif; ?>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?= $product->translation->title ?>
                                </td>
                                <td>
                                    <? if(!empty($product->category)): ?>
                                        <?= $product->category->translation->title ?>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? if(!empty($product->category)): ?>
                                        <?= $product->category->translation->description ?>
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
                                    ])?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    <? endif; ?>
                </table>
                <!-- TODO: languageId -->
                <a href="<?= Url::to(['/multishop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>
