<?php
use bl\cms\itpl\shop\backend\assets\EditProductAsset;
use bl\cms\shop\common\entities\Product;
use bl\multilang\entities\Language;
use bl\multilang\MultiLangUrlManager;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $selectedLanguage Language
 * @var $languages Language[]
 * @var $product Product
 * @var \yii\web\View $this
 * @var MultiLangUrlManager $urlManagerFrontend
 * @var $viewName string
 * @var $params array
 */

EditProductAsset::register($this);

if ($product->isNewRecord) {
    $this->title = \Yii::t('shop', 'Creating a new product');
}
else {
    $this->title = Yii::t('shop', 'Changing the product');
}


$newProductMessage = Yii::t('shop', 'You must save new product before this action');

$urlManagerFrontend = Yii::$app->urlManagerFrontend;

$this->params['breadcrumbs'] = [
    Yii::t('shop', 'Shop'),
    [
        'label' => Yii::t('shop', 'Products'),
        'url' => ['/shop/product'],
        'itemprop' => 'url'
    ]
];
$this->params['breadcrumbs'][] = (!empty($product->translation)) ? $product->translation->title : '';
?>

<!--BODY PANEL-->
<div class="tabs-container">

    <ul class="nav nav-tabs">
        <li class="<?= Yii::$app->controller->action->id == 'add-basic' || Yii::$app->controller->action->id == 'save' ? 'tab active' : 'tab'; ?>">
            <?= Html::a(\Yii::t('shop', 'Basic'), Url::to([
                'add-basic', 'id' => $product->id, 'languageId' => $selectedLanguage->id
            ]),
                [
                    'aria-expanded' => 'true',
                ]);
            ?>
        </li>

        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-image' ? 'active' : ''; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Photo'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Photo'), Url::to(['add-image', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true',
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-video' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Video'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Video'), Url::to(['add-video', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-price' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Prices'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Prices'), Url::to(['add-price', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-param' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Params'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Params'), Url::to(['add-param', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-file' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Files'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Files'), Url::to(['add-file', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-combination' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Combinations'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Combinations'), Url::to(['add-combination', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
        <li class="<?= (empty($product->translation)) ? 'disabled' : '';?> <?= Yii::$app->controller->action->id == 'add-additional' ? 'tab active' : 'tab'; ?>">
            <?=
            ($product->isNewRecord) ?
                Html::a(\Yii::t('shop', 'Additional products'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newProductMessage
                ]) :
                Html::a(\Yii::t('shop', 'Additional products'), Url::to(['add-additional', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]),
                    [
                        'aria-expanded' => 'true'
                    ]); ?>
        </li>
    </ul>

    <!--MODERATION-->
    <?php if (Yii::$app->user->can('moderateProductCreation') && $product->status == Product::STATUS_ON_MODERATION) : ?>
        <h2><?= \Yii::t('shop', 'Moderation'); ?></h2>
        <p><?= \Yii::t('shop', 'This product status is "on moderation". You may accept or decline it.'); ?></p>
        <?= Html::a(\Yii::t('shop', 'Accept'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_SUCCESS]), ['class' => 'btn btn-primary btn-xs']); ?>
        <?= Html::a(\Yii::t('shop', 'Decline'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_DECLINED]), ['class' => 'btn btn-danger btn-xs']); ?>
    <?php endif; ?>

    <div class="ibox-content ">

        <!--VIEW ON SITE-->
        <?php if (!empty($product->translation)) : ?>
            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-external-link']) . Html::tag('span', Yii::t('shop', 'View on website')),
                $urlManagerFrontend->createAbsoluteUrl(['/shop/product/show', 'id' => $product->id], true), [
                    'class' => 'btn btn-info btn-xs pull-right m-t-xs m-l-xs',
                    'target' => '_blank'
                ]); ?>
        <?php endif; ?>

        <!-- LANGUAGES -->
        <?= \bl\cms\shop\widgets\LanguageSwitcher::widget([
            'selectedLanguage' => $selectedLanguage,
        ]); ?>

        <!--CANCEL BUTTON-->
        <a href="<?= Url::to(['/shop/product']); ?>">
            <?= Html::button(\Yii::t('shop', 'Cancel'), [
                'class' => 'btn m-t-xs btn-danger btn-xs pull-right m-r-sm'
            ]); ?>
        </a>

        <!--CONTENT-->
        <?= $this->render($viewName, $params); ?>
    </div>

</div>