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

        <div>
            <?= Html::a(\Yii::t('shop', 'Clear cart'), Url::toRoute('/shop/cart/clear'), ['class' => 'btn btn-primary']); ?>
        </div>
    <?php endif; ?>


    <!--ORDER FORM-->
    <?php if (!empty($order)) : ?>
        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/shop/cart/make-order']
        ]); ?>

        <?= $form->field($order, 'first_name')->textInput()->label(\Yii::t('shop', 'First name')); ?>
        <?= $form->field($order, 'last_name')->textInput()->label(\Yii::t('shop', 'Last name')); ?>
        <?= $form->field($order, 'email')->textInput()->label(\Yii::t('shop', 'E-mail')); ?>
        <?= $form->field($order, 'phone')->textInput()->label(\Yii::t('shop', 'Phone number')); ?>
        <?= $form->field($order, 'address')->textInput()->label(\Yii::t('shop', 'Address')); ?>

        <?= Html::submitButton(Yii::t('shop', 'Submit'), [
            'class' => ''
        ]); ?>

        <?php $form::end(); ?>
    <?php endif; ?>


    <?php if (\Yii::$app->user->isGuest) : ?>
        <!--MODAL WINDOWS-->
        <!--Log in-->
        <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#loginModal">
            <?= \Yii::t('shop', 'Log in'); ?>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-primary"><?= \Yii::t('shop', 'Log in'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!--REGISTRATION-->
        <!--Log in-->
        <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal">
            <?= \Yii::t('shop', 'Register'); ?>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Название модали</h4>
                    </div>
                    <div class="modal-body">

                        <?= \bl\cms\cart\widgets\Register::widget([
                        ]) ?>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary"><?= \Yii::t('shop', 'Register'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
