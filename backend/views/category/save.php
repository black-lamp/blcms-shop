<?php
use bl\articles\backend\assets\TabsAsset;
use bl\cms\shop\backend\assets\InputTreeAsset;
use bl\cms\shop\backend\components\form\CategoryImageForm;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use marqu3s\summernote\Summernote;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
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
TabsAsset::register($this);

$this->title = \Yii::t('shop', 'Edit category');
?>

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

            <ul class="tabs">
                <li>
                    <?= Html::a(Yii::t('shop', 'Basic'), Url::to(['add-basic', 'categoryId' => $category->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(Yii::t('shop', 'SEO data'), Url::to(['add-seo', 'categoryId' => $category->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(Yii::t('shop', 'Images'), Url::to(['add-images', 'categoryId' => $category->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
            </ul>


            <? Pjax::begin([
                'linkSelector' => '.image',
                'enablePushState' => true,
                'timeout' => 10000
            ]);
            ?>

            <?= $this->render($viewName, $params);
            ?>
            <? Pjax::end(); ?>


            <a href="<?= Url::to(['/shop/category']); ?>">
                <?= Html::button(\Yii::t('shop', 'Close'), [
                    'class' => 'btn btn-danger pull-right'
                ]); ?>
            </a>
        </div>
    </div>
</div>