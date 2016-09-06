<?php
use bl\cms\shop\backend\assets\PjaxLoaderAsset;
use bl\cms\shop\common\entities\CategoryTranslation;
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

$this->title = \Yii::t('shop', 'Product categories');

PjaxLoaderAsset::register($this);
?>

<?php Pjax::begin([
    'linkSelector' => '.category-nav',
    'enablePushState' => false,
    'timeout' => 10000
]);
?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="<?= Url::to(['/shop/category/save', 'languageId' => Language::getCurrent()->id])?>" class="btn btn-primary btn-xs pull-right">
                        <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
                    </a>
                    <i class="glyphicon glyphicon-list"></i>
                    <?= \Yii::t('shop', 'Product categories'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <?php if(!empty($categories)): ?>
                            <thead>
                            <tr>
                                <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Position'); ?></th>
                                <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Name'); ?></th>
                                <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Parent'); ?></th>
                                <?php if(count($languages) > 1): ?>
                                    <th class="col-md-2 text-center"><?= \Yii::t('shop', 'Language'); ?></th>
                                <?php endif; ?>
                                <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Show'); ?></th>
                                <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Edit'); ?></th>
                                <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Delete'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($categories as $category): ?>
                                <tr>
                                    <td class="text-center">
                                        <?= $category->position ?>
                                        <a href="<?= Url::to([
                                            'up',
                                            'id' => $category->id
                                        ]) ?>" class="category-nav glyphicon glyphicon-arrow-up text-primary pull-left">
                                        </a>
                                        <a href="<?= Url::to([
                                            'down',
                                            'id' => $category->id
                                        ]) ?>" class="category-nav glyphicon glyphicon-arrow-down text-primary pull-left">
                                        </a>
                                    </td>
                                    <td>
                                        <?= $category->translation->title ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($category->parent)): ?>
                                            <?= $category->parent->translation->title ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(count($languages) > 1): ?>
                                            <?php $translations = ArrayHelper::index($category->translations, 'language_id') ?>
                                            <?php foreach ($languages as $language): ?>
                                                <a href="<?= Url::to([
                                                    'save',
                                                    'categoryId' => $category->id,
                                                    'languageId' => $language->id
                                                ]) ?>"
                                                   type="button"
                                                   class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                                   ?> btn-xs"><?= $language->name ?></a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?= Url::to([
                                            'switch-show',
                                            'id' => $category->id
                                        ]) ?>" class="category-nav">
                                            <?php if ($category->show): ?>
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            <?php else: ?>
                                                <i class="glyphicon glyphicon-minus text-danger"></i>
                                            <?php endif; ?>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?= Url::to(['save', 'categoryId' => $category->id, 'languageId' => Language::getCurrent()->id])?>"
                                           class="glyphicon glyphicon-edit text-warning btn btn-default btn-sm">
                                        </a>
                                        <br>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?= Url::to(['delete', 'id' => $category->id])?>"
                                           class="category-nav glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>
                    <a href="<?= Url::to(['/shop/category/save', 'languageId' => Language::getCurrent()->id])?>" class="btn btn-primary pull-right">
                        <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::end() ?>