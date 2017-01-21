<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $recommendedProducts \bl\cms\shop\common\entities\Product
 */
use bl\cms\cart\models\CartForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>

<?php if (!empty($recommendedProducts)) : ?>
    <div class="col-md-12 recommended-products">

        <!--WIDGET TITLE-->
        <p class="recommended-products-title">
            <?= Yii::t('shop', 'Recommended products') ?>
        </p>

        <!--PRODUCTS-->
        <?php foreach ($recommendedProducts as $recommendedProduct) : ?>
            <div class="text-center product col-md-3">

                <!--Product image-->
                <div class="img">
                    <a href="<?= Url::to(['/shop/product/show', 'id' => $recommendedProduct->id]) ?>">
                        <?php if (!empty($recommendedProduct->image)) : ?>
                            <?= Html::img($recommendedProduct->image->small) ?>
                        <?php endif; ?>
                    </a>
                </div>

                <!--Content block-->
                <?php $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => ['/cart/cart/add'],
                    'options' => [
                        '_fields' => [
                            'class' => 'col-md-4'
                        ]
                    ]
                ]);
                $cart = new CartForm();
                ?>
                <div class="product-content">
                    <!--Product title-->
                    <p class="product-title">
                        <a href="<?= Url::to(['/shop/product/show', 'id' => $recommendedProduct->id]) ?>">
                            <?= !empty($recommendedProduct->translation->title) ? $recommendedProduct->translation->title : ''; ?>
                        </a>
                    </p>

                    <div class="price-and-count">
                        <!--Price-->
                        <div class="price col-md-6">
                            <?php if (!empty($recommendedProduct->prices)) : ?>
                                <?= $form->field($cart, 'priceId', ['options' => ['class' => '']])->dropDownList(ArrayHelper::map($recommendedProduct->prices, 'id', function ($recommendedProduct) {
                                    $priceItem = $recommendedProduct->translation->title . ' - ' . \Yii::$app->formatter->asCurrency($recommendedProduct->price);
                                    return $priceItem;
                                }))->label(\Yii::t('shop', 'Price')); ?>
                            <?php else : ?>
                                <p class="label-price"><?= Yii::t('shop', 'Price'); ?></p>
                                <p class="standart-price">
                                    <?= \Yii::$app->formatter->asCurrency($recommendedProduct->price); ?>
                                </p>
                            <?php endif; ?>
                            <?= $form->field($cart, 'productId')->hiddenInput(['value' => $recommendedProduct->id])->label(false); ?>
                        </div>

                        <!--Count-->
                        <div class="count col-md-6">
                            <?= $form->field($cart, 'count', ['options' => ['class' => '']])->
                            textInput(['type' => 'number', 'min' => 1, 'value' => 1])->label(\Yii::t('shop', 'Count'));
                            ?>
                        </div>
                    </div>

                    <!--Button-->
                    <div class="buy">
                        <?= Html::submitButton(
                            Html::tag('span', '', ['class' => 'fa fa-shopping-cart']),
                            ['class' => 'btn btn-tight btn-primary']); ?>
                    </div>
                </div>
                <?php $form::end(); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>