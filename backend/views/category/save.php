<?php
use bl\cms\shop\backend\assets\EditCategoryAsset;
use bl\cms\shop\backend\components\form\UploadForm;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use marqu3s\summernote\Summernote;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov
 *
 * @var $categories Category[]
 * @var $item Category
 * @var $category_translation CategoryTranslation
 * @var $languages Language[]
 * @var $selectedLanguage Language
 * @var $categories Category[]
 * @var $minPosition Category
 * @var $maxPosition Category
 * @var $image_form UploadForm
 *
 */

EditCategoryAsset::register($this);
$this->title = \Yii::t('shop', 'Edit category');
?>


<? $addForm = ActiveForm::begin(['action' => Url::to(['/shop/category/save', 'categoryId' => $item->id, 'languageId' => $selectedLanguage->id]), 'method'=>'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <p><?= \Yii::t('shop', 'Edit category'); ?></p>
            </div>
            <div class="panel-body">

                <!-- TABS -->
                <div>
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active"><a href="#basic" aria-controls="home" role="tab" data-toggle="tab"><?= \Yii::t('shop', 'Basic');?></a></li>
                        <li><a href="#seo" aria-controls="profile" role="tab" data-toggle="tab"><?= \Yii::t('shop', 'SEO data');?></a></li>
                        <li><a href="#images" aria-controls="messages" role="tab" data-toggle="tab"><?= \Yii::t('shop', 'Image');?></a></li>
                    </ul>

                    <!-- Tabs content -->
                    <div class="tab-content">

                        <!-- BASIC -->
                        <div role="tabpanel" class="tab-pane active" id="basic">
                            <h2><?= \Yii::t('shop', 'Basic options');?></h2>
                            <!-- LANGUAGES -->
                            <? if(count($languages) > 1): ?>
                                <div class="dropdown">
                                    <button class="btn btn-warning btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <?= $selectedLanguage->name ?>
                                        <span class="caret"></span>
                                    </button>
                                    <? if(count($languages) > 1): ?>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <? foreach($languages as $language): ?>
                                                <li>
                                                    <a href="
                                        <?= Url::to([
                                                        'save',
                                                        'categoryId' => $item->id,
                                                        'languageId' => $language->id])?>
                                                ">
                                                        <?= $language->name?>
                                                    </a>
                                                </li>
                                            <? endforeach; ?>
                                        </ul>
                                    <? endif; ?>
                                </div>
                            <? endif; ?>

                            <!-- NAME -->
                            <?= $addForm->field($category_translation, 'title', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->label(\Yii::t('shop', 'Name'))
                            ?>

                            <!-- SHOW -->
                            <?= $addForm->field($item, 'show', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->checkbox(['class' => 'i-checks','checked ' => ($item->show) ? '' : false])
                            ?>

                            <!-- PARENT -->
                            <b><?= \Yii::t('shop', 'Parent category');?></b>
                            <?php
                            $categoriesWithoutParent = Category::find()->where(['parent_id' => null])->all();
                            $tree = Category::findChilds($categoriesWithoutParent);
                            echo '<ul class="list-group ul-treefree ul-dropfree">';
                            echo treeRecoursion($tree);
                            echo '</ul>';
                            ?>
                            <?php
                            function treeRecoursion($tree) {
                                foreach ($tree as $oneCategory) {
                                    if (!empty($oneCategory['childCategory'])) {
                                        echo '<li class="list-group-item"><input type="radio" name="Category[parent_id]" value="'
                                            . $oneCategory[0]->id . '"  id="' . $oneCategory[0]->id . '"' . '><label for="'
                                            . $oneCategory[0]->id . '">'
                                            . $oneCategory[0]->translation->title
                                            . '</label>';
                                        echo '<ul class="list-group">';
                                        treeRecoursion($oneCategory['childCategory']);
                                        echo '</ul></li>';
                                    }
                                    else {
                                        echo '<li class="list-group-item"><input type="radio" name="Category[parent_id]" value="'
                                            . $oneCategory[0]->id . '"  id="' . $oneCategory[0]->id . '"><label for="'
                                            . $oneCategory[0]->id . '">'
                                            . $oneCategory[0]->translation->title
                                            . '</label>';
                                        echo '</li>';                                }
                                }
                            }
                            ?>

                            <!-- DESCRIPTION -->
                            <?= $addForm->field($category_translation, 'description', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->widget(Summernote::className())->label(\Yii::t('shop', 'Description'))
                            ?>

                            <!-- SORT ORDER -->
                            <?= $addForm->field($item, 'position', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]])->textInput([
                                'type' => 'number',
                                'max' => $maxPosition,
                                'min' => $minPosition
                            ]); ?>

                        </div>

                        <!-- SEO -->
                        <div role="tabpanel" class="tab-pane fade" id="seo">
                            <h2><?= \Yii::t('shop', 'SEO options');?></h2>
                            <?= $addForm->field($category_translation, 'seoUrl', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->label('SEO URL')
                            ?>
                            <?= $addForm->field($category_translation, 'seoTitle', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->label(\Yii::t('shop', 'SEO title'))
                            ?>
                            <?= $addForm->field($category_translation, 'seoDescription')->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO description'));
                            ?>
                            <?= $addForm->field($category_translation, 'seoKeywords')->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO keywords'))
                            ?>
                        </div>

                        <!-- IMAGE -->
                        <div role="tabpanel" class="tab-pane fade" id="images">
                            <h2><?= \Yii::t('shop', 'Image');?></h2>
                            <table class="table table-bordered table-stripped">
                                <tr>
                                    <th>
                                        <?= \Yii::t('shop', 'Type');?>
                                    </th>
                                    <th>
                                        <?= \Yii::t('shop', 'Image preview');?>
                                    </th>
                                    <th>
                                        <?= \Yii::t('shop', 'Image URL');?>
                                    </th>
                                    <th>
                                        <?= \Yii::t('shop', 'Actions');?>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <?= \Yii::t('shop', 'Menu item'); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->menu_item)) : ?>
                                            <img src="/images/shop-category/menu_item/<?=$item->menu_item . '-thumb.jpg' ;?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->menu_item)) : ?>
                                            <input type="text" class="form-control" disabled="" value="<?= '/images/shop-category/menu_item/' . $item->menu_item . '-menu_item.jpg'; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->menu_item)) : ?>
                                            <p><b><?= \Yii::t('shop', 'Delete image');?></b></p>
                                            <a href="<?= Url::toRoute(['delete-image', 'id' => $item->id, 'type' => 'menu_item']);?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                        <?php endif; ?>
                                        <?= $addForm->field($image_form, 'menu_item')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= \Yii::t('shop', 'Thumbnail'); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->thumbnail)) : ?>
                                            <img src="/images/shop-category/thumbnail/<?=$item->thumbnail . '-thumb.jpg' ;?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->thumbnail)) : ?>
                                            <input type="text" class="form-control" disabled="" value="<?= '/images/shop-category/thumbnail/' . $item->thumbnail . '-menu_item.jpg'; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->thumbnail)) : ?>
                                            <p><b><?= \Yii::t('shop', 'Delete image');?></b></p>
                                            <a href="<?= Url::toRoute(['delete-image', 'id' => $item->id, 'type' => 'thumbnail']);?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                        <?php endif; ?>
                                        <?= $addForm->field($image_form, 'thumbnail')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= \Yii::t('shop', 'Cover'); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->cover)) : ?>
                                            <img src="/images/shop-category/cover/<?=$item->cover . '-thumb.jpg' ;?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->cover)) : ?>
                                            <input type="text" class="form-control" disabled="" value="<?= '/images/shop-category/cover/' . $item->cover . '-menu_item.jpg'; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->cover)) : ?>
                                            <p><b><?= \Yii::t('shop', 'Delete image');?></b></p>
                                            <a href="<?= Url::toRoute(['delete-image', 'id' => $item->id, 'type' => 'cover']);?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                        <?php endif; ?>
                                        <?= $addForm->field($image_form, 'cover')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">
            </div>
        </div>
    </div>

<? ActiveForm::end(); ?>