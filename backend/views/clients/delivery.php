<?php
use bl\cms\shop\backend\components\form\DeliveryForm;
use dosamigos\tinymce\TinyMce;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var DeliveryForm $model
 * @var string $message
 */
?>



<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                Клиенты
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!empty($message)): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($model)): ?>
                            <?php $form = ActiveForm::begin(['method' => 'post']) ?>
                            <?= $form->field($model, 'subject')->label('Тема') ?>
                            <?= $form->field($model, 'text', [
                                'inputOptions' => [
                                    'class' => 'form-control'
                                ]
                            ])->widget(TinyMce::className(), [
                                'options' => ['rows' => 10],
                                'language' => 'ru',
                                'clientOptions' => [
                                    'relative_urls' => false,
                                    'remove_script_host' => false,
                                    'plugins' => [
                                        'textcolor colorpicker',
                                        "advlist autolink lists link charmap print preview anchor",
                                        "searchreplace visualblocks code fullscreen",
                                        "insertdatetime media table contextmenu paste",
                                        'image'
                                    ],
                                    'toolbar' => "undo redo | forecolor backcolor | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                                ]
                            ])->label('Сообщение')
                            ?>

                            <?= Html::submitButton('<i class="fa fa-send-o"></i>  Отправить', [
                                'class' => 'btn btn-primary pull-right'
                            ]) ?>
                            <?php $form->end(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
