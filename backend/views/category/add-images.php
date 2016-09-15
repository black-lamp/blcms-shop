<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php $addForm = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>

    <h2><?= \Yii::t('shop', 'Images'); ?></h2>
    <table class="table-bordered table-condensed table-stripped table-hover">
        <thead class="thead-inverse">
        <tr>
            <th class="text-center col-md-1">
                <?= \Yii::t('shop', 'Type'); ?>
            </th>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
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
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
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
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td>
                    <?php if (!empty($category->menu_item)) : ?>
                        <img data-toggle="modal" data-target="#menuItemModal"
                             src="/images/shop-category/menu_item/<?= $category->menu_item . '-small.jpg'; ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="menuItemModal" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="/images/shop-category/menu_item/<?= $category->menu_item . '-thumb.jpg'; ?>">
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($category->menu_item)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-category/menu_item/' . $category->menu_item . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'menu_item')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($category->menu_item)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'imageType' => 'menu_item', 'languageId' => $languageId]); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <tr>
            <td class="text-center">
                <?= \Yii::t('shop', 'Thumbnail'); ?>
            </td>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td>
                    <?php if (!empty($category->thumbnail)) : ?>
                    <img data-toggle="modal" data-target="#thumbnailModal"
                         src="/images/shop-category/thumbnail/<?= $category->thumbnail . '-small.jpg'; ?>"
                         class="thumb">
                    <!-- Modal -->
                    <div id="thumbnailModal" class="modal fade" role="dialog">
                        <img style="display: block" class="modal-dialog"
                             src="/images/shop-category/thumbnail/<?= $category->thumbnail . '-thumb.jpg'; ?>">
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($category->thumbnail)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-category/thumbnail/' . $category->thumbnail . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'thumbnail')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($category->thumbnail)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'imageType' => 'thumbnail', 'languageId' => $languageId]); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <tr>
            <td class="text-center">
                <?= \Yii::t('shop', 'Cover'); ?>
            </td>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td>
                    <?php if (!empty($category->cover)) : ?>
                        <img data-toggle="modal" data-target="#coverModal"
                             src="/images/shop-category/cover/<?= $category->cover . '-small.jpg'; ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="coverModal" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="/images/shop-category/cover/<?= $category->cover . '-thumb.jpg'; ?>">
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($category->cover)) : ?>
                        <input type="text" class="form-control" disabled=""
                               value="<?= '/images/shop-category/cover/' . $category->cover . '-big.jpg'; ?>">
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td>
                <?= $addForm->field($image_form, 'cover')->fileInput()->label(\Yii::t('shop', 'Upload image')); ?>
            </td>
            <?php if (!empty($category->menu_item) || !empty($category->thumbnail) || !empty($category->cover)) : ?>
                <td class="text-center">
                    <?php if (!empty($category->cover)) : ?>
                        <a href="<?= Url::toRoute(['delete-image', 'id' => $category->id, 'imageType' => 'cover', 'languageId' => $languageId]); ?>"
                           class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        </tbody>
    </table>

    <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

<?php $addForm::end(); ?>