<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\shop\frontend\components\ProductSearch
 * @var $dataProvider bl\cms\shop\frontend\components\ProductSearch
 *
 * @var $category Category
 * @var $menuItems Category
 * @var $filters Filter
 * @var $products Product
 * @var $cart \bl\cms\cart\models\CartForm
 *
 */

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\frontend\assets\CategoryAsset;
use bl\cms\shop\frontend\components\widgets\ProductFilter;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ListView;
use yii\widgets\Pjax;

CategoryAsset::register($this);

$shop = (!empty($category->translation->title)) ?
    [
        'label' => Yii::t('frontend/navigation', 'Магазин'),
        'url' => (!empty($category)) ? Url::toRoute(['/shop']) : false,
        'itemprop' => 'url',
    ] : Yii::t('frontend/navigation', 'Магазин');
?>

<!--BREADCRUMBS-->
<div>
    <?= Breadcrumbs::widget([
        'itemTemplate' => '<li><b><span>{link}</span></b></li>',
        'homeLink' => [
            'label' => Yii::t('frontend/navigation', 'Главная'),
            'url' => Url::toRoute(['/']),
            'itemprop' => 'url',
        ],
        'links' => (!empty($category)) ? [$shop, $category->translation->title]
            : [$shop]
    ]);
    ?>
</div>

<!--TITLE-->
<?php if (!empty($category->translation->title)) : ?>
    <h1><?= $category->translation->title; ?></h1>
<?php endif; ?>


<?php Pjax::begin([
    'linkSelector' => '.pjax'
]); ?>

<!--PRODUCTS-->
<div class="col-md-<?= (!empty($category)) ? '9' : '12'; ?>">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'tag' => 'div',
            'class' => 'products',
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
            'class' => 'media',
        ],
        'emptyText' => \Yii::t('shop', 'The list is empty'),

        'itemView' => '_product'
    ]); ?>
    <?php
    ?>
</div>

<!--FILTERING-->
<?php if (!empty($category)) : ?>
<div class="col-md-2">
    <h3><?= \Yii::t('shop', 'Filtering') ?></h3>
    <?= ProductFilter::widget([
        'category' => $category,
        'filters' => $filters,
        'searchModel' => $searchModel]);
    ?>
</div>
<?php endif; ?>
<?php Pjax::end(); ?>
