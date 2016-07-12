<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\backend\components\form\VendorImageForm;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Vendor $vendor
 * @var VendorImageForm $image_form
 */

?>



<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Vendor' ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data']
                    ]) ?>

                <div class="col-md-12">
                    <!--TITLE-->
                    <?= $form->field($vendor, 'title') ?>

                    <!--IMAGE-->
                    <h2>Image</h2>
                    <?php if(!empty($vendor->image_name)): ?>
                        <?= Html::img($image_form->getBig($vendor->image_name)) ?>
                    <?php endif; ?>
                    <?= $form->field($image_form, 'imageFile')->fileInput() ?>

                    <?= Html::submitButton('Save', [
                        'class' => 'btn btn-default'
                    ]) ?>
                </div>

                <?php $form->end(); ?>
            </div>
        </div>
    </div>
</div>
