<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * RelatedProduct[] $relatedProducts
 */
use bl\cms\shop\frontend\widgets\ProductPrices;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php if (!empty($relatedProducts)): ?>
    <div class="products">
        <?php foreach ($relatedProducts as $relatedProduct): ?>

            <?php $url = Url::toRoute(['/shop/product/show', 'id' => $relatedProduct->relatedProduct->id]); ?>
            <?php $alt = (!empty($relatedProduct->relatedProduct->image->translation)) ? $relatedProduct->relatedProduct->image->translation->alt : ''; ?>
            <?php $imageUrl = $relatedProduct->relatedProduct->image ? $relatedProduct->relatedProduct->image->getThumb() : ''; ?>

            <div class="product">
                <div>
                    <header>
                        <a href="<?= $url; ?>" style="background-image: url(<?= $imageUrl; ?>);">
                            <?= $alt; ?>
                        </a>

                        <?php if (!empty($relatedProduct->relatedProduct->translation->title)): ?>
                            <div class="title-mask">
                                <a href="<?= $url; ?>">
                                </a>
                            </div>
                            <p class="h3">
                                <a href="<?= $url; ?>">
                                    <?= Html::encode($relatedProduct->relatedProduct->translation->title); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </header>

                    <!--PRICES-->
                    <?php $form = ActiveForm::begin([
                        'action' => ['/cart/cart/add'],
                        'options' => [
                            'class' => 'order-form'
                        ]]);
                    ?>
                    <?= ProductPrices::widget([
                        'product' => $relatedProduct->relatedProduct,
                        'form' => $form,
                        'defaultCombination' => $relatedProduct->relatedProduct->defaultCombination,
                        'notAvailableText' => \Yii::t('shop', 'Not available')
                    ]) ?>
                    <?php $form->end() ?>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
<?php endif; ?>
