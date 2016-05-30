<?php
use bl\cms\shop\common\entities\Vendor;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Vendor $vendor
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
                <? $form = ActiveForm::begin(['method' => 'post']) ?>

                    <?= $form->field($vendor, 'title') ?>

                    <?= Html::submitButton('Save', [
                        'class' => 'btn btn-default'
                    ]) ?>

                <? $form->end(); ?>
            </div>
        </div>
    </div>
</div>
