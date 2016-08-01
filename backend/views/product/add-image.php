<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $image_form ProductImageForm
 */

use bl\cms\shop\backend\components\form\ProductImageForm;
use bl\cms\shop\common\entities\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<p><?= \Yii::t('shop', 'Upload image or copy from web'); ?></p>
<? $uploadImageForm = ActiveForm::begin([
    'action' => [
        'product/upload-image',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'image',
        'data-pjax' => true
    ]
]);
?>
<table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
    <thead class="thead-inverse">
    <tr>
        <th class="col-md-4">
            <?= \Yii::t('shop', 'Image'); ?>
        </th>
        <th class="col-md-5">
            <?= \Yii::t('shop', 'Alt'); ?>
        </th>
        <th class="col-md-3">
            <?= \Yii::t('shop', 'Add'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <?= $uploadImageForm->field($image_form, 'image')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
        </td>
        <td></td>
        <td>
            <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'media btn btn-primary']) ?>
        </td>
    </tr>
    </tbody>
</table>
<? $uploadImageForm->end(); ?>

<? $copyImageForm = ActiveForm::begin([
    'action' => [
        'product/copy-image',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'image',
        'data-pjax' => true
    ]
]);
?>
<table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
    <tbody>
        <tr>
            <td class="col-md-4">
                <?= $copyImageForm->field($image_form, 'link')->label(\Yii::t('shop', 'Insert image link')); ?>
            </td>
            <td class="col-md-5"></td>
            <td class="col-md-3">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'media btn btn-primary']) ?>
            </td>
        </tr>
    </tbody>
</table>
<? $copyImageForm->end(); ?>

<div role="tabpanel" class="tab-pane" id="images">
    <h2><?= \Yii::t('shop', 'Image'); ?></h2>
    <table class="table-bordered table-condensed table-stripped table-hover">
        <thead class="thead-inverse">
        <?php if (!empty($product->images)) : ?>
        <tr>
            <th class="text-center col-md-2">
                <?= \Yii::t('shop', 'Image preview'); ?>
            </th>
            <th class="text-center col-md-5">
                <?= \Yii::t('shop', 'Image URL'); ?>
            </th>
            <th class="text-center col-md-5">
                <?= \Yii::t('shop', 'Alt'); ?>
            </th>
            <th class="text-center col-md-1">
                <?= \Yii::t('shop', 'Delete'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($product->images as $image) : ?>
            <tr>
                <td class="text-center">
                    <img data-toggle="modal" data-target="#menuItemModal"
                         src="/images/shop-product/<?= $image->file_name . '-small.jpg'; ?>"
                         class="thumb">
                    <!-- Modal -->
                    <div id="menuItemModal" class="modal fade" role="dialog">
                        <img style="display: block" class="modal-dialog"
                             src="/images/shop-product/<?= $image->file_name . '-thumb.jpg'; ?>">
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control" disabled=""
                           value="<?= '/images/shop-product/menu_item/' . $image->file_name . '-big.jpg'; ?>">
                </td>
                <td>
                    <?= $image->alt; ?>
                </td>
                <td class="text-center">
                    <a href="<?= Url::toRoute(['delete-image', 'id' => $image->id]); ?>"
                       class="media glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php endif; ?>

        </tbody>
    </table>
</div>
