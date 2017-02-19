<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $combinations \bl\cms\shop\common\entities\Combination[]
 * @var $combination \bl\cms\shop\common\entities\Combination
 * @var $combinationAttributeForm bl\cms\shop\backend\components\form\CombinationAttributeForm
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $languageId integer
 * @var $image_form \bl\cms\shop\backend\components\form\CombinationImageForm
 *
 */

use bl\cms\shop\backend\assets\EditCombinationAsset;
use bl\cms\shop\common\entities\PriceDiscountType;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\imagable\helpers\FileHelper;
use marqu3s\summernote\Summernote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

EditCombinationAsset::register($this);

?>

<!--COMBINATIONS LIST-->
<?php if (!empty($combinations)): ?>
    <h2><?= Yii::t('shop', 'Combinations list'); ?></h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Combination'); ?></th>
            <th class="col-md-2 text-center"><?= \Yii::t('shop', 'Attributes'); ?></th>
            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Prices'); ?></th>
            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Default'); ?></th>
            <th class="col-md-3 text-center"><?= \Yii::t('shop', 'Images'); ?></th>
            <th class="col-md-1 text-center"><?= \Yii::t('shop', 'Control'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($combinations as $combination): ?>
            <tr>
                <td class="text-center">
                    <?= \Yii::t('shop', 'SKU') . ': ' . $combination->sku; ?>
                    <p>
                        <i><?= (!empty($combination->combinationAvailability)) ?
                                $combination->combinationAvailability->translation->title : ''; ?></i>
                    </p>
                    <?php if (!empty($combination->number)): ?>
                        <p>(<?= $combination->number; ?>)</p>
                    <?php endif; ?>
                </td>
                <td>
                    <?php foreach ($combination->combinationAttributes as $combinationAttribute) : ?>
                        <p>
                            <?= '<b>' . $combinationAttribute->productAttribute->translation->title . '</b>'; ?>:

                            <?php if ($combinationAttribute->productAttribute->type_id == ShopAttribute::TYPE_COLOR): ?>
                                <?= $combinationAttribute->productAttributeValue->translation->colorTexture->attributeColor; ?>
                            <?php elseif ($combinationAttribute->productAttribute->type_id == ShopAttribute::TYPE_TEXTURE): ?>
                                <?= $combinationAttribute->productAttributeValue->translation->colorTexture->attributeTexture; ?>
                            <?php else: ?>
                                <?= $combinationAttribute->productAttributeValue->translation->value; ?>
                            <?php endif; ?>
                        </p>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php foreach ($combination->prices as $price) : ?>
                        <?php if (!empty($price->price)) : ?>
                            <p>
                                <b><?= $price->combinationPrice->userGroup->translation->title; ?></b>:
                                <span><?= \Yii::$app->formatter->asCurrency($price->discountPrice); ?></span>
                                <s><?= \Yii::$app->formatter->asCurrency($price->oldPrice); ?></s>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <td class="text-center">
                    <?php if ($combination->default) : ?>
                        <i class="fa fa-plus"></i>
                    <?php else : ?>
                        <a href="<?= Url::to(['change-default-combination', 'combinationId' => $combination->id]); ?>">
                            <i class="fa fa-minus"></i>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php foreach ($combination->images as $image): ?>
                        <?= Html::img('/images/shop-product/' . FileHelper::getFullName(
                                \Yii::$app->shop_imagable->getSmall('shop-product', $image->productImage->file_name)),
                            [
                                'style' => 'max-width: 50px; max-height: 100px;'
                            ]
                        ); ?>
                    <?php endforeach; ?>
                </td>
                <td class="text-center">
                    <?= Html::a('', [
                        'edit-combination',
                        'combinationId' => $combination->id,
                        'languageId' => $languageId
                    ],
                        [
                            'class' => 'glyphicon glyphicon-edit text-warnong btn btn-default btn-sm'
                        ]
                    ) ?>
                    <?= Html::a('', [
                        'remove-combination',
                        'combinationId' => $combination->id,
                    ],
                        [
                            'class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<!-- Button trigger modal -->
<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
    <?= \Yii::t('shop', 'Add new combination'); ?>
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= \Yii::t('shop', 'New combination'); ?></h4>
            </div>

            <?= $this->render('new-combination-form', [
                'product' => $product,
                'languageId' => $languageId,
                'combination' => $combination,
                'combinationTranslation' => $combinationTranslation,
                'image_form' => $image_form,
                'prices' => $prices,
                'combinationAttributeForm' => $combinationAttributeForm,
                'productImages' => $productImages
            ]); ?>

        </div>
    </div>
</div>
