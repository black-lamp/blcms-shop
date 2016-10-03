<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="content">


    <!--PRODUCTS TABLE-->
    <?php if (!empty($products)) : ?>
        <div>
            <?= Html::a(\Yii::t('shop', 'Clear cart'), Url::toRoute('/shop/cart/clear'), ['class' => 'btn btn-primary']); ?>
        </div>
        <table class="table table-hover table-striped">
            <tr>
                <th class="col-md-1 text-center">Id</th>
                <th class="col-md-7 text-center">Title</th>
                <th class="col-md-3 text-center">Price</th>
                <th class="col-md-2 text-center">Count</th>
            </tr>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td class="text-center">
                        <?= $product->id; ?>
                    </td>
                    <td class="text-center">
                        <?= Html::a($product->translation->title, Url::to(['/shop/product/show', 'id' => $product->id])); ?>
                    </td>
                    <td class="text-center">
                        <?= $product->price; ?>
                    </td>
                    <td class="text-center">
                        <?= $product->count; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (\Yii::$app->user->isGuest) : ?>
            <!--MODAL WINDOWS-->


            <!--REGISTRATION-->
            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal">
                <?= \Yii::t('shop', 'I\'m a new user'); ?>
            </button>
            <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel"><?= Yii::t('shop', 'Registration'); ?></h4>
                        </div>

                        <?= \bl\cms\cart\widgets\Register::widget([
                        ]) ?>
                    </div>
                </div>
            </div>

            <!--LOGIN-->
            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#loginModal">
                <?= \Yii::t('shop', 'I already have an account'); ?>
            </button>
            <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Название модали</h4>
                        </div>
                        <div class="modal-body">

                            <?= \dektrium\user\widgets\Login::widget([
                            ]) ?>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>


    <!--ORDER FORM-->
    <?php if (!empty($order)) : ?>
        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/shop/cart/make-order']
        ]); ?>

        <!--Name-->
        <?php if (!empty(Yii::$app->user->identity->profile->name)) : ?>
            <p>
                <b><?=Yii::t('shop', 'Name') ?>:</b> <?= Yii::$app->user->identity->profile->name; ?>
            </p>
        <?php else : ?>
            <?= $form->field($order, 'first_name')->textInput()->label(\Yii::t('shop', 'First name')); ?>
        <?php endif; ?>

        <!--Surname-->
        <?php if (!empty(Yii::$app->user->identity->profile->surname)) : ?>
            <p>
                <b><?=Yii::t('shop', 'Surname') ?>:</b> <?= Yii::$app->user->identity->profile->surname; ?>
            </p>
        <?php else : ?>
            <?= $form->field($order, 'last_name')->textInput()->label(\Yii::t('shop', 'Last name')); ?>
        <?php endif; ?>

        <!--Email-->
        <?php if (!empty(Yii::$app->user->identity->email)) : ?>
            <p>
                <b><?=Yii::t('shop', 'E-mail') ?>:</b> <?= Yii::$app->user->identity->email; ?>
            </p>
        <?php else : ?>
            <?= $form->field($order, 'email')->textInput()->label(\Yii::t('shop', 'E-mail')); ?>
        <?php endif; ?>

        <!--Phone-->
        <?php if (!empty(Yii::$app->user->identity->profile->phone)) : ?>
            <p>
                <b><?=Yii::t('shop', 'Phone') ?>:</b> <?= Yii::$app->user->identity->profile->phone; ?>
            </p>
        <?php else : ?>
            <?= $form->field($order, 'phone')->textInput()->label(\Yii::t('shop', 'Phone')); ?>
        <?php endif; ?>

        <!--Address-->
        <h4><?= \Yii::t('shop', 'Address'); ?></h4>
        <?= $form->field($address, 'country')->textInput(); ?>
        <?= $form->field($address, 'region')->textInput(); ?>
        <?= $form->field($address, 'city')->textInput(); ?>
        <?= $form->field($address, 'house')->textInput(); ?>
        <?= $form->field($address, 'apartment')->textInput(); ?>
        <?= $form->field($address, 'zipcode')->textInput(); ?>

        <?= Html::submitButton(Yii::t('shop', 'Submit'), [
            'class' => ''
        ]); ?>

        <?php $form::end(); ?>
    <?php endif; ?>

</div>
