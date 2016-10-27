<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $recommendedProducts \bl\cms\shop\common\entities\Product
 */
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>

<?php if (!empty($recommendedProducts)) : ?>
    <div class="col-md-12 recommended-products">
        <p class="recommended-products-title"><?= Yii::t('shop', 'Recommended products') ?></p>
        <?php foreach ($recommendedProducts as $recommendedProduct) : ?>
            <div class="text-center product col-md-3">
                <a href="<?= Url::to(['product/show', 'id' => $recommendedProduct->id]) ?>">
                    <div class="img">
                        <?php if (!empty($recommendedProduct->image)) : ?>
                            <?= Html::img($recommendedProduct->image->small) ?>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <p class="title">
                            <?= !empty($recommendedProduct->translation->title) ? $recommendedProduct->translation->title : ''; ?>
                        </p>

                        <?php if (!empty($recommendedProduct->prices)) : ?>
                            <?= Html::activeDropDownList(
                                $recommendedProduct,
                                'price',
                                ArrayHelper::map($recommendedProduct->prices, 'id', function($model) {
                                    $price = \Yii::$app->formatter->asCurrency($model->salePrice);

                                    return $price;
                                })
                            ); ?>
                        <?php else : ?>
                            <p class="price">
                                <?= \Yii::$app->formatter->asCurrency($recommendedProduct->price); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>