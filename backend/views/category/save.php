<?php
use bl\cms\shop\backend\assets\InputTreeAsset;
use bl\cms\shop\backend\components\form\CategoryImageForm;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use marqu3s\summernote\Summernote;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov
 *
 * @var $category Category
 * @var $category_translation CategoryTranslation
 * @var $categories Category[]
 * @var $selectedLanguage Language
 * @var $languages Language[]
 * @var $image_form CategoryImageForm
 * @var $minPosition Category
 * @var $maxPosition Category
 * @var $categoriesTree Category
 */

InputTreeAsset::register($this);
$this->title = \Yii::t('shop', 'Edit category');
?>

<? $addForm = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?php if (!empty($category->id)) : ?>
                    <?php if (!empty($category_translation->title)) : ?>
                        <span>
                    <?= \Yii::t('shop', 'Edit category'); ?>
                </span>
                    <?php else: ?>
                        <span>
                    <?= \Yii::t('shop', 'Add category translation'); ?>
                </span>
                    <?php endif; ?>
                <?php else : ?>
                    <span>
                    <?= \Yii::t('shop', 'Add new category'); ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="panel-body">

                <!-- TABS -->
                <div>
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active"><a href="#basic" aria-controls="home" role="tab"
                                              data-toggle="tab"><?= \Yii::t('shop', 'Basic'); ?></a></li>
                        <li><a href="#seo" aria-controls="profile" role="tab"
                               data-toggle="tab"><?= \Yii::t('shop', 'SEO data'); ?></a></li>
                        <li><a href="#images" aria-controls="messages" role="tab"
                               data-toggle="tab"><?= \Yii::t('shop', 'Image'); ?></a></li>
                    </ul>

                    <!-- Tabs content -->
                    <div class="tab-content">

                        <!-- BASIC -->
                        <div role="tabpanel" class="tab-pane active" id="basic">
                            <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>
                            <!-- LANGUAGES -->
                            <? if (count($languages) > 1): ?>
                                <div class="dropdown">
                                    <button class="btn btn-warning btn-xs dropdown-toggle" type="button"
                                            id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="true">
                                        <?= $selectedLanguage->name ?>
                                        <span class="caret"></span>
                                    </button>
                                    <? if (count($languages) > 1): ?>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <? foreach ($languages as $language): ?>
                                                <li>
                                                    <a href="
                                        <?= Url::to([
                                                        'save',
                                                        'categoryId' => $category->id,
                                                        'languageId' => $language->id]) ?>
                                                ">
                                                        <?= $language->name ?>
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
                            <?= $addForm->field($category, 'show', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->checkbox(['class' => 'i-checks', 'checked ' => ($category->show) ? '' : false])
                            ?>

                            <!-- PARENT CATEGORY -->
                             <b><?= \Yii::t('shop', 'Parent category'); ?></b>
                            <?= '<ul class="list-group ul-treefree ul-dropfree">'; ?>
                            <?= '<li class="list-group-item"><input type="radio" name="Category[parent_id]" value="" id="null"><label for="null">' . \Yii::t("shop", "Without parent") . '</label>'; ?>
                            <?= CategoryTranslation::treeRecoursion($categoriesTree, $category->id); ?>
                            <?= '</ul>'; ?>

                            <!-- DESCRIPTION -->
                            <?= $addForm->field($category_translation, 'description', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->widget(Summernote::className())->label(\Yii::t('shop', 'Description'))
                            ?>

                            <!-- SORT ORDER -->
                            <?= $addForm->field($category, 'position', [
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
                            <h2><?= \Yii::t('shop', 'SEO options'); ?></h2>
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
                            <h2><?= \Yii::t('shop', 'Image'); ?></h2>
                            <table class="table-bordered table-condensed table-stripped table-hover">
                                <thead class="thead-inverse">
                                <tr>
                                    <th class="text-center col-md-1">
                                        <?= \Yii::t('shop', 'Type'); ?>
                                    </th>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <th class="text-center col-md-2">
                                            <?= \Yii::t('shop', 'Image preview'); ?>
                                        </th>
                                        <th class="text-center col-md-5">
                                            <?= \Yii::t('shop', 'Image URL'); ?>
                                        </th>
                                    <?php endif; ?>
                                    <th class="text-center col-md-3">
                                        <?= \Yii::t('shop', 'Upload'); ?>
                                    </th>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <th class="text-center col-md-1">
                                            <?= \Yii::t('shop', 'Delete'); ?>
                                        </th>
                                    <?php endif; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center">
                                        <?= \Yii::t('shop', 'Menu item'); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td>
                                            <?php if (!empty($category->menu_item)) : ?>
                                                <img data-toggle="modal" data-target="#menuItemModal"
                                                     src="/images/shop-category/menu_item/<?= $category->menu_item . '-small.jpg'; ?>"
                                                     class="thumb">
                                                <!-- Modal -->
                                                <div id="menuItemModal" class="modal fade" role="dialog">
                                                    <img style="display: block" class="modal-dialog"
                                                         src="/images/shop-category/menu_item/<?= $category->menu_item . '-thumb.jpg'; ?>">
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($category->menu_item)) : ?>
                                                <input type="text" class="form-control" disabled=""
                                                       value="<?= '/images/shop-category/menu_item/' . $category->menu_item . '-big.jpg'; ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?= $addForm->field($image_form, 'menu_item')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td class="text-center">
                                            <?php if (!empty($category->menu_item)) : ?>
                                                <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'type' => 'menu_item']); ?>"
                                                   class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <?= \Yii::t('shop', 'Thumbnail'); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td>
                                            <?php if (!empty($category->thumbnail)) : ?>
                                            <img data-toggle="modal" data-target="#thumbnailModal"
                                                 src="/images/shop-category/thumbnail/<?= $category->thumbnail . '-small.jpg'; ?>"
                                                 class="thumb">
                                            <!-- Modal -->
                                            <div id="thumbnailModal" class="modal fade" role="dialog">
                                                <img style="display: block" class="modal-dialog"
                                                     src="/images/shop-category/thumbnail/<?= $category->thumbnail . '-thumb.jpg'; ?>">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($category->thumbnail)) : ?>
                                                <input type="text" class="form-control" disabled=""
                                                       value="<?= '/images/shop-category/thumbnail/' . $category->thumbnail . '-big.jpg'; ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?= $addForm->field($image_form, 'thumbnail')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td class="text-center">
                                            <?php if (!empty($category->thumbnail)) : ?>
                                                <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'type' => 'thumbnail']); ?>"
                                                   class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <?= \Yii::t('shop', 'Cover'); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td>
                                            <?php if (!empty($category->cover)) : ?>
                                                <img data-toggle="modal" data-target="#coverModal"
                                                     src="/images/shop-category/cover/<?= $category->cover . '-small.jpg'; ?>"
                                                     class="thumb">
                                                <!-- Modal -->
                                                <div id="coverModal" class="modal fade" role="dialog">
                                                    <img style="display: block" class="modal-dialog"
                                                         src="/images/shop-category/cover/<?= $category->cover . '-thumb.jpg'; ?>">
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($category->cover)) : ?>
                                                <input type="text" class="form-control" disabled=""
                                                       value="<?= '/images/shop-category/cover/' . $category->cover . '-big.jpg'; ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?= $addForm->field($image_form, 'cover')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
                                    </td>
                                    <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                                        <td class="text-center">
                                            <?php if (!empty($category->cover)) : ?>
                                                <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'type' => 'cover']); ?>"
                                                   class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">
            </div>
        </div>
    </div>

<? ActiveForm::end(); ?>