<?php
use bl\cms\shop\backend\assets\EditProductAsset;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\multilang\entities\Language;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $languages Language[]
 * @var $selectedLanguage Language
 * @var $product Product
 * @var $products_translation ProductTranslation
 * @var $params_translation ParamTranslation
 * @var $categories CategoryTranslation[]
 */

EditProductAsset::register($this);

$this->title = \Yii::t('shop', 'Edit product');
$newProductMessage = Yii::t('shop', 'You must save new product before this action');

$this->params['breadcrumbs'] = [
    Yii::t('shop', 'Shop'), [
        'label' => Yii::t('shop', 'Products'),
        'url' => ['/shop/product'],
        'itemprop' => 'url'
    ]
];
$this->params['breadcrumbs'][] = (!empty($product->translation)) ? $product->translation->title : Yii::t('shop', 'New product');;
?>

<div class="col-md-12">
    <div class="panel panel-default">

        <!--HEADER PANEL-->
        <div class="panel-heading">
            <i class="glyphicon glyphicon-list"></i>
            <?php if (!empty($product->id)) : ?>
                <?php if (!empty($products_translation->title)) : ?>
                    <span>
                    <?= (!empty($product->translation->title)) ?
                        \Yii::t('shop', 'Edit product') . ' "' . $product->translation->title . '"' :
                        \Yii::t('shop', 'Edit product');
                    ?>
                </span>
                <?php else: ?>
                    <span>
                    <?= \Yii::t('shop', 'Add product translation'); ?>
                </span>
                <?php endif; ?>
            <?php else : ?>
                <span>
                <?= \Yii::t('shop', 'Add new product'); ?>
            </span>
            <?php endif; ?>

            <!-- LANGUAGES -->
            <?php if (count($languages) > 1): ?>
                <div class="dropdown pull-right">
                    <button class="btn btn-warning btn-xs dropdown-toggle" type="button"
                            id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="true">
                        <?= $selectedLanguage->name ?>
                        <span class="caret"></span>
                    </button>
                    <?php if (count($languages) > 1): ?>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <?php foreach ($languages as $language): ?>
                                <li>
                                    <a href="
                                        <?= Url::to([
                                        'save',
                                        'id' => $product->id,
                                        'languageId' => $language->id]) ?>
                                                ">
                                        <?= $language->name ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!--BODY PANEL-->
        <div class="panel-body">
            <!--MODERATION-->
            <?php if (Yii::$app->user->can('moderateProductCreation') && $product->status == Product::STATUS_ON_MODERATION) : ?>
                <h2><?= \Yii::t('shop', 'Moderation'); ?></h2>
                <p><?= \Yii::t('shop', 'This product status is "on moderation". You may accept or decline it.'); ?></p>
                <?= Html::a(\Yii::t('shop', 'Accept'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_SUCCESS]), ['class' => 'btn btn-primary btn-xs']); ?>
                <?= Html::a(\Yii::t('shop', 'Decline'), Url::toRoute(['change-product-status', 'id' => $product->id, 'status' => Product::STATUS_DECLINED]), ['class' => 'btn btn-danger btn-xs']); ?>
            <?php endif; ?>

            <ul class="nav nav-tabs">
                <li class="<?= Yii::$app->controller->action->id == 'add-basic' || Yii::$app->controller->action->id == 'save' ? 'tab active' : 'tab'; ?>">
                    <?= Html::a(\Yii::t('shop', 'Basic'), Url::to(['add-basic', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                        [
                            'aria-expanded' => 'true'
                        ]); ?>
                </li>

                <li class="<?= Yii::$app->controller->action->id == 'add-image' ? 'active' : ''; ?>">
                    <?=
                    ($product->isNewRecord) ?
                        '<a>' . \Yii::t('shop', 'Photo') . '</a>' :
                        Html::a(\Yii::t('shop', 'Photo'), Url::to(['add-image', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'aria-expanded' => 'true'
                            ]); ?>
                </li>
                <li class="<?= Yii::$app->controller->action->id == 'add-video' ? 'tab active' : 'tab'; ?>">
                    <?=
                    ($product->isNewRecord) ?
                        '<a>' . \Yii::t('shop', 'Video') . '</a>' :

                        Html::a(\Yii::t('shop', 'Video'), Url::to(['add-video', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'aria-expanded' => 'true'
                            ]); ?>
                </li>
                <li class="<?= Yii::$app->controller->action->id == 'add-price' ? 'tab active' : 'tab'; ?>">
                    <?=
                    ($product->isNewRecord) ?
                        '<a>' . \Yii::t('shop', 'Prices') . '</a>' :
                        Html::a(\Yii::t('shop', 'Prices'), Url::to(['add-price', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'aria-expanded' => 'true'
                            ]); ?>
                </li>
                <li class="<?= Yii::$app->controller->action->id == 'add-param' ? 'tab active' : 'tab'; ?>">
                    <?=
                    ($product->isNewRecord) ?
                        '<a>' . \Yii::t('shop', 'Params') . '</a>' :
                        Html::a(\Yii::t('shop', 'Params'), Url::to(['add-param', 'id' => $product->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'aria-expanded' => 'true'
                            ]); ?>
                </li>
            </ul>

            <!--CONTENT-->
            <?= $this->render($viewName, $params); ?>
        </div>
    </div>
</div>