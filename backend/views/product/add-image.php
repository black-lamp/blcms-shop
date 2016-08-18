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

    <br>

    <div role="tabpanel" class="tab-pane" id="images">
        <table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
            <thead class="thead-inverse">
            <tr>
                <th class="text-center" colspan="4">
                    <h2><?= \Yii::t('shop', 'Photo'); ?></h2>
                </th>
            </tr>
            <?php if (!empty($product->images)) : ?>
            <tr>
                <th class="text-center col-md-2">
                    <?= \Yii::t('shop', 'Image preview'); ?>
                </th>
                <th class="text-center col-md-4">
                    <?= \Yii::t('shop', 'Image URL'); ?>
                </th>
                <th class="text-center col-md-4">
                    <?= \Yii::t('shop', 'Alt'); ?>
                </th>
                <th class="text-center col-md-2">
                    <?= \Yii::t('shop', 'Manage'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($product->images as $image) : ?>
                <tr>
                    <td class="text-center col-md-2">
                        <img data-toggle="modal" data-target="#menuItemModal-<?= $image->id ?>"
                             src="/images/shop-product/<?= $image->file_name . '-small.jpg'; ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="menuItemModal-<?= $image->id ?>" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="/images/shop-product/<?= $image->file_name . '-thumb.jpg'; ?>">
                        </div>
                    </td>
                    <td class="text-center col-md-4">
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-product/menu_item/' . $image->file_name . '-big.jpg'; ?>">
                    </td>
                    <td class="text-center col-md-4">
                        <?= $image->alt; ?>
                    </td>
                    <td class="text-center col-md-2">
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $image->id]); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<? $addImageForm = ActiveForm::begin([
    'action' => [
        'product/add-image',
        'productId' => $product->id,
        'languageId' => $selectedLanguage->id
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
        <tr class="text-center">
            <td class="col-md-2">
                <strong>
                    <?= \Yii::t('shop', 'Add from web'); ?>
                </strong>
            </td>
            <td class="col-md-4">
                <?= $addImageForm->field($image_form, 'link')->textInput([
                    'placeholder' => Yii::t('shop', 'Image link')
                ])->label(false); ?>
            </td>
            <td class="col-md-4">
                <?= $addImageForm->field($image_form, 'alt')->textInput(['placeholder' => \Yii::t('shop', 'Alternative text')])->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
        <tbody>
        <tr>
            <td class="col-md-2 text-center">
                <strong>
                    <?= \Yii::t('shop', 'Upload'); ?>
                </strong>
            </td>
            <td class="col-md-4">
                <?= $addImageForm->field($image_form, 'image')->fileInput()->label(false); ?>
            </td>
            <td class="text-center col-md-4">
                <?= $addImageForm->field($image_form, 'alt')->textInput(['placeholder' => \Yii::t('shop', 'Alternative text')])->label(false); ?>
            </td>
            <td class="text-center col-md-2">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
        </tbody>
    </table>
<? $addImageForm->end(); ?>