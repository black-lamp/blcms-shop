<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $order \bl\cms\cart\models\Order
 * @var $profile \bl\cms\shop\common\components\user\models\Profile
 * @var $user \bl\cms\shop\common\components\user\models\User
 * @var $address \bl\cms\shop\common\components\user\models\UserAddress
 * @var $productsFromDB \bl\cms\cart\models\OrderProduct
 * @var $productsFromSession \bl\cms\shop\common\entities\Product
 */

use bl\cms\cart\widgets\Delivery;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use bl\cms\shop\frontend\assets\CartAsset;

$this->title = \Yii::t('shop', 'Cart');

CartAsset::register($this);
?>

<div class="content cart col-md-12">
    <div class="row">
        <h1 class="text-center"><?= $this->title; ?></h1>
    </div>

    <!--PRODUCTS TABLE-->


    <table class="table table-hover table-striped products-list">
        <tr>
            <th class="col-md-4 text-center"><?= Yii::t('cart', 'Title'); ?></th>
            <th class="col-md-3 text-center"><?= Yii::t('cart', 'Photo'); ?></th>
            <th class="col-md-2 text-center"><?= Yii::t('cart', 'Price'); ?></th>
            <th class="col-md-2 text-center"><?= Yii::t('cart', 'Count'); ?></th>
            <th class="col-md-1"></th>
        </tr>

        <!--PRODUCT LIST FROM SESSION-->
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td class="text-center">
                        <?= Html::a($product->translation->title, Url::to(['/shop/product/show', 'id' => $product->id])); ?>
                    </td>
                    <td class="text-center">
                        <?php if (!empty($product->image)) : ?>
                            <?= Html::a(Html::img($product->image->small),
                                Url::to(['/shop/product/show', 'id' => $product->id])); ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if (!empty($product->price)) : ?>
                            <span class="price">
                                <?= \Yii::$app->formatter->asCurrency($product->price); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?= $product->count; ?>
                    </td>
                    <td class="text-center">
                        <?=Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
                            Url::to(['shop/cart/remove', 'id' => $product->id]),
                            [
                                'class' => 'btn btn-danger btn-xs',
                                'title' => \Yii::t('cart', 'Remove')
                            ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if (\Yii::$app->user->isGuest && Yii::$app->cart->saveToDataBase == true) : ?>
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
    <?php else : ?>
        <!--ORDER FORM-->
        <?php if (!empty($order)) : ?>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/shop/cart/make-order']
            ]); ?>

            <!--PERSONAL DATA-->
            <div class="personal-data">
                <h3><?= Yii::t('cart', 'Your personal data'); ?>:</h3>

                <!--Name-->
                <?php if (!empty(Yii::$app->user->identity->profile->name)) : ?>
                    <p>
                        <b><?= Yii::t('shop', 'Name') ?>:</b> <?= Yii::$app->user->identity->profile->name; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'name')->textInput(); ?>
                <?php endif; ?>

                <!--Patronomic-->
                <?php if (!empty(Yii::$app->user->identity->profile->patronymic)) : ?>
                    <p>
                        <b><?= Yii::t('shop', 'Patronomic') ?>
                            :</b> <?= Yii::$app->user->identity->profile->patronymic; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'patronymic')->textInput(); ?>
                <?php endif; ?>

                <!--Surname-->
                <?php if (!empty(Yii::$app->user->identity->profile->surname)) : ?>
                    <p>
                        <b><?= Yii::t('shop', 'Surname') ?>:</b> <?= Yii::$app->user->identity->profile->surname; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'surname')->textInput(); ?>
                <?php endif; ?>

                <!--Email-->
                <?php if (!empty(Yii::$app->user->identity->email)) : ?>
                    <p>
                        <b><?= Yii::t('shop', 'E-mail') ?>:</b> <?= Yii::$app->user->identity->email; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($user, 'email')->textInput(); ?>
                <?php endif; ?>

                <!--Phone-->
                <?php if (!empty(Yii::$app->user->identity->profile->phone)) : ?>
                    <p>
                        <b><?= Yii::t('shop', 'Phone number') ?>:</b> <?= Yii::$app->user->identity->profile->phone; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'phone')->textInput(); ?>
                <?php endif; ?>

            </div>

            <br>
            <!--DELIVERY METHOD-->
            <?= Delivery::widget(['form' => $form, 'model' => $order, 'config' => [
                'addressModel' => $address
            ]]); ?>

            <!--Address selecting-->
            <div class="address">
                <h3><?= Yii::t('cart', 'Address'); ?>:</h3>

                <?php if (!empty(\Yii::$app->user->identity->profile->userAddresses)) : ?>
                    <?= $form->field($order, 'address_id')
                        ->dropDownList(ArrayHelper::map(\Yii::$app->user->identity->profile->userAddresses, 'id', function ($model) {
                            $address = (!empty($model->city)) ? $model->city . ', ' : '';
                            $address .= (!empty($model->street)) ? Yii::t('cart', 'st.') . $model->street . ', ' : '';
                            $address .= (!empty($model->house)) ? Yii::t('cart', 'hse.') . $model->house . ' - ' : '';
                            $address .= (!empty($model->apartment)) ? Yii::t('cart', 'apt.') . $model->apartment : '';
                            return $address;
                        }),
                            ['prompt' => \Yii::t('shop', 'Select address')])->label(\Yii::t('shop', 'Select address or enter it at the next fields')); ?>
                <?php endif; ?>

                <!--Address-->
                <h4><?= \Yii::t('shop', 'Address'); ?></h4>
                <?= $form->field($address, 'country')->textInput(); ?>
                <?= $form->field($address, 'region')->textInput(); ?>
                <?= $form->field($address, 'city')->textInput(); ?>
                <?= $form->field($address, 'street')->textInput(); ?>
                <?= $form->field($address, 'house')->textInput(); ?>
                <?= $form->field($address, 'apartment')->textInput(); ?>
                <?= $form->field($address, 'zipcode')->textInput(); ?>


            </div>

            <!--CLEAR BUTTON-->
            <?= Html::a(\Yii::t('shop', 'Clear cart'), Url::toRoute('/shop/cart/clear'), ['class' => 'btn btn-primary pull-right']); ?>
            <!--SUBMIT BUTTON-->
            <?= Html::submitButton(Yii::t('shop', 'Make order'), [
                'class' => 'btn btn-danger'
            ]); ?>

            <?php $form::end(); ?>
        <?php endif; ?>
    <?php endif; ?>


</div>

