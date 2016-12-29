<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $renderView string
 * @var $params array
 *
 * Ex.: echo \bl\cms\shop\frontend\widgets\ProductPrices::widget([
 *  'product' => $product,
 *  'form' => $form,
 *  'defaultCombination' => $defaultCombination
 * ]);
 */
use kartik\touchspin\TouchSpin;
use yii\bootstrap\Html;
?>

<div class="product-prices-widget" data-not-available-text="<?= $params['notAvailableText']; ?>">
    <?= $this->render($renderView, $params); ?>

    <?= $params['form']->field($params['cart'], 'productId', [
        'template' => '{input}',
        'options' => []
    ])
        ->hiddenInput(['value' => $params['product']->id, 'id' => 'productId'])
        ->label(false) ?>


    <?php if ($params['showCounter']): ?>
    <div class="form-group">
        <div class="quantity">
            <h3 class="title">
                <?= Yii::t('shop', 'Count') ?>
            </h3>
                <?= $params['form']->field($params['cart'], 'count', [
                    'enableClientValidation' => false
                ])->widget(TouchSpin::className(), [
                    'options' => [
                        'value' => 1
                    ],
                    'pluginOptions' => [
                        'pluginOptions' => [
                            'min' => 1
                        ],
                        'buttonup_class' => 'btn btn-primary',
                        'buttondown_class' => 'btn btn-info',
                        'buttonup_txt' => '<i class="glyphicon glyphicon glyphicon-plus"></i>',
                        'buttondown_txt' => '<i class="glyphicon glyphicon-minus"></i>',
                    ],
                ])
                    ->label(false) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php $icon = Html::tag('i', '', ['class' => 'si-shopping-cart']); ?>
    <?= Html::submitButton($icon . Yii::t('shop', 'To cart'), ['class' => 'btn btn-primary cart-btn', 'id' => 'add-to-cart-button']) ?>

</div>