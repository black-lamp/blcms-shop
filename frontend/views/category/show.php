<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $menuItems Category
 * @var $category Category
 * @var $products Product
 * @var $filters Filter
 */

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\frontend\assets\ProductAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\widgets\ListView;
use yii\widgets\Pjax;

ProductAsset::register($this);

$shop = (!empty($category->translation->title)) ?
    [
        'label' => Yii::t('frontend/navigation', 'Магазин'),
        'url' => (!empty($category)) ? Url::toRoute(['/shop']) : false,
        'itemprop' => 'url',
    ] : Yii::t('frontend/navigation', 'Магазин');
$links = (!empty($category)) ? [$shop, $category->translation->title] : [$shop];
?>

<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
    <?= Breadcrumbs::widget([
        'itemTemplate' => '<li><b><span itemprop="title">{link}</span></b></li>',
        'homeLink' => [
            'label' => Yii::t('frontend/navigation', 'Главная'),
            'url' => Url::toRoute(['/']),
            'itemprop' => 'url',
        ],
        'links' => $links
    ]);
    ?>
</div>
<h1 class="text-center"><?=$category->translation->title; ?></h1>
<div class="row products">
    <div class="col-md-12">

        <?php Pjax::begin([
            'enablePushState' => false,
            'enableReplaceState' => false,
            'timeout' => 10000,
        ]); ?>

        <div class="col-md-2 menu-categories">
            <?php foreach ($menuItems as $menuItem) : ?>
                <p>
                    <a href="<?= Url::toRoute(['category/show', 'id' => $menuItem->id]); ?>">
                        <?= $menuItem->translation->title ?>
                    </a>
                </p>
            <?php endforeach; ?>
        </div>
        <div class="col-md-8">

            <?= ListView::widget([
                'dataProvider' => $dataProvider,

                'options' => [
                    'tag' => 'div',
                    'class' => '',
                    'id' => '',
                ],

                'layout' => "{pager}\n{items}\n{pager}",
                'summary' => '{count} ' . \Yii::t('shop', 'from') . ' {totalCount}',
                'summaryOptions' => [
                    'tag' => 'span',
                    'class' => ''
                ],

                'itemOptions' => [
                    'tag' => 'div',
                    'class' => 'product-card',
                ],

                'emptyText' => \Yii::t('shop', 'The list is empty'),

                'itemView' => function ($model, $key, $index, $widget) {

                    /*Title*/
                    $item = Html::a(Html::tag('h3', Html::encode($model->translation->title), ['class' => 'title']),
                        Url::toRoute(['/shop/product/show', 'id' => $model->id]));
                    /*Image*/
                    if (!empty($model->images[0]->file_name)) {
                        $item .= Html::tag('div', '', ['style' => 'background: url(' . ProductImage::getThumb($model->images[0]->file_name) . ');',
                            'class' => 'product-img']);
                    }

                    /*Price*/

                    if (!empty($model->prices[0]->price) || !empty($model->prices[0]->sale)) {
                        if (!empty($model->prices[0]->price)) {
                            $item .= Html::tag('div', \Yii::$app->formatter->asCurrency($model->prices[0]->price), ['class' => 'new']);
                        }
                        if (!empty($model->prices[0]->sale)) {
                            $item .= Html::tag('div', \Yii::$app->formatter->asCurrency($model->prices[0]->salePrice), ['class' => 'text-muted']);
                        }
                    }
                    return $item;
                },

            ]);
            ?>


        </div>

        <!--FILTERING-->
        <div class="col-md-2">
            <h3><?=\Yii::t('shop', 'Filtering') ?></h3>
            <?php $form = ActiveForm::begin([
                'action' => ['show'],
                'method' => 'get',
                'options' => ['data-pjax' => true]
            ]);
            ?>

            <?= Html::hiddenInput('id', $category->id); ?>

            <?php if ($filters->filter_by_country) : ?>
                <?= $form->field($searchModel, 'country_id')
                    ->dropDownList(ArrayHelper::map(ProductCountry::find()->all(), 'id', 'translation.title'),
                        ['prompt' => ''])->label(\Yii::t('shop', 'by country')) ?>
            <?php endif; ?>
            <?php if ($filters->filter_by_vendor) : ?>
                <?= $form->field($searchModel, 'vendor_id')
                    ->dropDownList(ArrayHelper::map(Vendor::find()->all(), 'id', 'title'),
                        ['prompt' => ''])->label(\Yii::t('shop', 'by vendor')) ?>
            <?php endif; ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('shop', 'Filter'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <?php Pjax::end(); ?>

    </div>
</div>




