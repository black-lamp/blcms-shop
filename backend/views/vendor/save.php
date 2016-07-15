<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\backend\components\form\VendorImage;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 * @author Nozhenko Vyacheslav <vv.nojenko@gmail.com>
 *
 * @var Vendor $vendor
 * @var VendorImage $vendor_image
 */

$this->title = Yii::t('shop', 'Save vendor');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= Html::encode($this->title); ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data']
                    ]) ?>

                <div class="col-md-offset-2 col-md-8 rig">
                    <!--TITLE INPUT-->
                    <?= $form->field($vendor, 'title')->label(Yii::t('shop','Title')) ?>

                    <!--IMAGE-->
                    <h4><?= Yii::t('shop', 'Logo'); ?></h4>
                    <?php if(!empty($vendor->image_name)): ?>
                        <?= Html::img($vendor_image->getBig($vendor->image_name), ['class' => 'img-thumbnail thumbnail center-block']) ?>
                    <?php endif; ?>

                    <!--IMAGE INPUT-->
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 text-center">
                            <?= $form->field($vendor_image, 'imageFile')->fileInput()->label(Yii::t('shop', 'Upload image')); ?>
                        </div>
                    </div>

                    <!--SUBMIT-->
                    <?= Html::submitButton(Yii::t('shop', 'Save'), [
                        'class' => 'btn btn-success pull-right'
                    ]) ?>
                </div>

                <?php $form->end(); ?>
            </div>
        </div>
    </div>
</div>
