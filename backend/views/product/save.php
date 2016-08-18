<?php
use bl\articles\backend\assets\TabsAsset;
use bl\cms\shop\backend\assets\EditProductAsset;
use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ProductCountryTranslation;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\ProductVideo;
use bl\cms\shop\common\entities\Vendor;
use bl\multilang\entities\Language;
use marqu3s\summernote\Summernote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\JuiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

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
TabsAsset::register($this);

$this->title = \Yii::t('shop', 'Edit product');
$newProductMessage = Yii::t('shop', 'You must save new product before this action');
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
            <? if (count($languages) > 1): ?>
                <div class="dropdown pull-right">
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
                                        'productId' => $product->id,
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
        </div>

        <!--BODY PANEL-->
        <div class="panel-body">

            <ul class="tabs">
                <li>
                    <?= Html::a(\Yii::t('shop', 'Basic'), Url::to(['add-basic', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(\Yii::t('shop', 'Photo'), Url::to(['add-image', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(\Yii::t('shop', 'Video'), Url::to(['add-video', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(\Yii::t('shop', 'Prices'), Url::to(['add-price', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
                </li>
                <li>
                    <?= Html::a(\Yii::t('shop', 'Params'), Url::to(['add-param', 'productId' => $product->id, 'languageId' => $selectedLanguage->id]), ['class' => 'image']); ?>
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
            
        </div>
    </div>
</div>