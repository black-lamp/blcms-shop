<?php
use bl\cms\shop\backend\components\form\VendorImage;
use bl\cms\shop\common\entities\Vendor;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Vendor[] $vendors
 * @var VendorImage $vendor_images
 */

$this->title = Yii::t('shop', 'Vendor List');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= Html::encode($this->title); ?>
            </div>
            <div>
                <div class="panel-body">
                    <table class="table-bordered table-condensed table-hover">
                        <?php if (!empty($vendors)): ?>
                            <thead>
                            <tr>
                                <th><?= 'Id' ?></th>
                                <th class="col-xs-6 col-sm-4 col-md-3"><?= 'Image' ?></th>
                                <th class="col-md-12 col-sm-12 col-xs-6"><?= 'Title' ?></th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($vendors as $vendor): ?>
                                <tr>
                                    <td>
                                        <?= $vendor->id ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($vendor->image_name)): ?>
                                            <a href="<?= $vendor_images->getBig($vendor->image_name) ?>" target="blank">
                                                <?= Html::img(
                                                    $vendor_images->getBig($vendor->image_name),
                                                    ['class' => 'img-responsive center-block']
                                                )?>
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= $vendor->title ?>
                                    </td>

                                    <td>
                                        <?= Html::a('', [
                                            'save',
                                            'id' => $vendor->id
                                        ], [
                                            'class' => 'glyphicon glyphicon-edit text-warning btn btn-warning btn-sm'
                                        ]); ?>
                                    </td>

                                    <td>
                                        <div class="text-center">
                                        <?= Html::a('', [
                                            'remove',
                                            'id' => $vendor->id
                                        ], [
                                            'class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm'
                                        ]); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <? endif; ?>
                    </table>
                    <div class="row-fluid" style="margin-top: 15px;">
                        <a href="<?= Url::to(['save']); ?>"
                           class="btn btn-primary pull-right">
                            <i class="fa fa-user-plus"></i> <?= Yii::t('yii', 'Add'); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
