<?php
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $categories CategoryTranslation */
/* @var $languages Language[] */

$this->title = 'Product category list';
?>

<h1>Categories</h1>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Categories list'?>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <? if(!empty($categories)): ?>
                        <thead>
                        <tr>
                            <th class="col-md-3"><?= 'Name'?></th>
                            <th class="col-md-2"><?= 'Parent name'?></th>
                            <? if(count($languages) > 1): ?>
                                <th class="col-lg-3"><?= 'Language' ?></th>
                            <? endif; ?>
                            <th class="text-center">Show</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($categories as $category): ?>
                            <tr>
                                <td>
                                    <?= $category->translation->title ?>
                                </td>
                                <td>
                                    <? if(!empty($category->parent)): ?>
                                        <?= $category->parent->translation->title ?>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? if(count($languages) > 1): ?>
                                        <? $translations = ArrayHelper::index($category->translations, 'language_id') ?>
                                        <? foreach ($languages as $language): ?>
                                            <a href="<?= Url::to([
                                                'save',
                                                'categoryId' => $category->id,
                                                'languageId' => $language->id
                                            ]) ?>"
                                               type="button"
                                               class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                               ?> btn-xs"><?= $language->name ?></a>
                                        <? endforeach; ?>
                                    <? endif; ?>
                                </td>

                                <td class="text-center">
<!--                                    <a href="--><?//= Url::to([
//                                        'switch-show',
//                                        'id' => $category->id
//                                    ]) ?><!--">-->
<!--                                        --><?// if ($category->show): ?>
<!--                                            <i class="glyphicon glyphicon-ok text-primary"></i>-->
<!--                                        --><?// else: ?>
<!--                                            <i class="glyphicon glyphicon-minus text-danger"></i>-->
<!--                                        --><?// endif; ?>
<!--                                    </a>-->
                                </td>

                                <td>
                                    <a href="<?= Url::to(['save', 'categoryId' => $category->id, 'languageId' => Language::getCurrent()->id])?>"
                                       class="glyphicon glyphicon-edit text-warning btn btn-default btn-sm">
                                    </a>
                                    <br>
                                </td>

                                <td>
                                    <a href="<?= Url::to(['delete', 'id' => $category->id])?>"
                                       class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    <? endif; ?>
                </table>
                <a href="<?= Url::to(['/shop/category/save', 'languageId' => Language::getCurrent()->id])?>" class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>