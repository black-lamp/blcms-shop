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
use bl\cms\shop\common\entities\Vendor;
use bl\multilang\entities\Language;
use dektrium\user\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\widgets\ListView;
use yii\widgets\Pjax;

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
<div class="row products">
    <div class="col-md-12">
        <div class="col-md-3 menu-categories">
            <?php foreach ($menuItems as $menuItem) : ?>
                <p>
                    <a href="<?= Url::toRoute(['category/show', 'id' => $menuItem->id]); ?>">
                        <?= $menuItem->translation->title ?>
                    </a>
                </p>
            <?php endforeach; ?>
        </div>
        <div class="col-md-6">

            <?php Pjax::begin([
                'enablePushState' => false,
                'enableReplaceState' => false,
                'timeout' => 10000,
            ]); ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,

                'options' => [
                    'tag' => 'div',
                    'class' => '',
                    'id' => '',
                ],

                'layout' => "{pager}\n{summary}\n{items}\n{pager}",
                'summary' => '{count} ' . \Yii::t('shop', 'from') . ' {totalCount}',
                'summaryOptions' => [
                    'tag' => 'span',
                    'class' => ''
                ],

                'itemOptions' => [
                    'tag' => 'div',
                    'class' => '',
                ],

                'emptyText' => \Yii::t('shop', 'The list is empty'),

                'itemView' => function ($model, $key, $index, $widget) {

                    $item = Html::a(Html::tag('span', Html::encode($model->translation->title), ['class' => 'title']),
                        Url::toRoute(['/shop/product/show', 'id' => $model->id]));
                    $item .= Html::tag('p', $model->translation->description);
                    if (!empty($model->prices[0]->price)) {
                        $item .= Html::tag('span', $model->prices[0]->currencySalePrice, ['class' => 'new']);
                    }
                    if (!empty($model->prices[0]->sale)) {
                        $item .= Html::tag('span', $model->prices[0]->currencyPrice, ['class' => 'text-muted']);
                    }
                    return $item;
                },

            ]);
            ?>

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

            <?php Pjax::end(); ?>
        </div>

        <!--FILTERING-->
        <div class="col-md-3">

        </div>
    </div>
</div>




