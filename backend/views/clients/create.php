<?php
use bl\cms\shop\common\entities\Clients;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Clients $model
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                Добавить клиента
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $form = ActiveForm::begin(['method' => 'post']) ?>
                        <?= $form->field($model, 'name')->label('Имя') ?>
                        <?= $form->field($model, 'email')->label('Email') ?>
                        <?= $form->field($model, 'phone')->label('Номер телефона') ?>
                        <?= Html::submitButton('Добавить', [
                            'class' => 'btn btn-primary'
                        ]) ?>
                        <?php $form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>