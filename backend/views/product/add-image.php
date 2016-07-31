<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 */

use bl\cms\shop\common\entities\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

//var_dump($product);
//
//if (!empty($product->cover)) {
//    echo $product->cover;
//}
//if (!empty($product->thumbnail)) {
//    echo $product->thumbnail;
//}
//if (!empty($product->menu_item)) {
//    echo $product->menu_item;
//}

?>
<? $addForm = ActiveForm::begin([
    'action' => [
        'product/add-image',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'image',
        'data-pjax' => true
    ]
]);
?>
<div role="tabpanel" class="tab-pane" id="images">
    <h2><?= \Yii::t('shop', 'Image'); ?></h2>
    <table class="table-bordered table-condensed table-stripped table-hover">
        <thead class="thead-inverse">
        <tr>
            <th class="text-center col-md-1">
                <?= \Yii::t('shop', 'Type'); ?>
            </th>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <th class="text-center col-md-2">
                    <?= \Yii::t('shop', 'Image preview'); ?>
                </th>
                <th class="text-center col-md-5">
                    <?= \Yii::t('shop', 'Image URL'); ?>
                </th>
            <?php endif; ?>
            <th class="text-center col-md-3">
                <?= \Yii::t('shop', 'Upload'); ?>
            </th>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <th class="text-center col-md-1">
                    <?= \Yii::t('shop', 'Delete'); ?>
                </th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center">
                <?= \Yii::t('shop', 'Menu item'); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td>
                    <?php if (!empty($product->menu_item)) : ?>
                        <img data-toggle="modal" data-target="#menuItemModal"
                             src="/images/shop-product/menu_item/<?= $product->menu_item . '-small.jpg'; ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="menuItemModal" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="/images/shop-product/menu_item/<?= $product->menu_item . '-thumb.jpg'; ?>">
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($product->menu_item)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-product/menu_item/' . $product->menu_item . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'menu_item')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($product->menu_item)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $product->id, 'type' => 'menu_item']); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <tr>
            <td class="text-center">
                <?= \Yii::t('shop', 'Thumbnail'); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td>
                    <?php if (!empty($product->thumbnail)) : ?>
                    <img data-toggle="modal" data-target="#thumbnailModal"
                         src="/images/shop-product/thumbnail/<?= $product->thumbnail . '-small.jpg'; ?>"
                         class="thumb">
                    <!-- Modal -->
                    <div id="thumbnailModal" class="modal fade" role="dialog">
                        <img style="display: block" class="modal-dialog"
                             src="/images/shop-product/thumbnail/<?= $product->thumbnail . '-thumb.jpg'; ?>">
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($product->thumbnail)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-product/thumbnail/' . $product->thumbnail . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'thumbnail')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($product->thumbnail)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $product->id, 'type' => 'thumbnail']); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <tr>
            <td class="text-center">
                <?= \Yii::t('shop', 'Cover'); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td>
                    <?php if (!empty($product->cover)) : ?>
                        <img data-toggle="modal" data-target="#coverModal"
                             src="/images/shop-product/cover/<?= $product->cover . '-small.jpg'; ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="coverModal" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="/images/shop-product/cover/<?= $product->cover . '-thumb.jpg'; ?>">
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($product->cover)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-product/cover/' . $product->cover . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'cover')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($product->menu_item) || !empty($product->thumbnail) || !empty($product->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($product->cover)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $product->id, 'type' => 'cover']); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        </tbody>
    </table>
</div>
<?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>

<? $addForm->end(); ?>
