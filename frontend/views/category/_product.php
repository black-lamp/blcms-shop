<?php
/**
 * @var Product $model
 */

use bl\cms\cart\models\CartForm;
use bl\cms\shop\common\entities\Product;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$modelUrl = Url::to(['/shop/product/show',
    'id' => $model->id
]);
?>

<div class="thumbnail">
    <span>
        <mark>
            <?= Yii::t('shop', 'Art. {articulus}', [
                'articulus' => $model->articulus
            ]) ?>
        </mark>
    </span>

    <?php if (!empty($model->image->small)): ?>
        <a href="<?= $modelUrl ?>">
            <?= Html::img($model->image->small, [
                'alt' => $model->image->translation->alt
            ]) ?>
        </a>
    <?php endif ?>

    <div class="caption">
        <a href="<?= $modelUrl ?>">
            <p class="h4"><?= $model->translation->title ?></p>
        </a>

        <small class="text-muted"><?= StringHelper::truncate($model->translation->description, 120) ?></small>

        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin([
                    'action' => ['/cart/cart/add'],
                    'options' => [
                        'class' => 'col-md-9 row'
                    ]]);
                $cart = new CartForm();
                ?>
                <?= $form->field($cart, 'productId', [
                    'template' => '{input}',
                    'options' => []
                ])
                    ->hiddenInput(['value' => $model->id])
                    ->label(false);
                ?>

                <?= $form->field($cart, 'count', [
                    'template' => '{input}',
                    'options' => []
                ])
                    ->hiddenInput(['value' => 1])
                    ->label(false);
                ?>

                <div class="help-block"></div>

                <!--PRICE-->
                <?php if (!empty($model->price)): ?>
                    <small><?= Yii::t('shop', 'Price'); ?>:</small>
                    <?php if (!empty($model->prices[0]->sale)): ?>
                        <strong><?= Yii::$app->formatter->asCurrency($model->prices[0]->salePrice); ?></strong>
                        <strike><?= Yii::$app->formatter->asCurrency($model->price); ?></strike>
                    <?php else: ?>
                        <strong><?= Yii::$app->formatter->asCurrency($model->price); ?></strong>
                    <?php endif ?>
                <?php endif ?>

                <div class="help-block"></div>

                <button type="submit" class="btn btn-success">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                    <?= Yii::t('shop', 'Add to cart'); ?>
                </button>
                <?php $form->end() ?>

                <?php if (!Yii::$app->user->isGuest && !$model->isFavorite()): ?>
                    <?php $addFavoriteProductUrl = Url::to(['/shop/favorite-product/add', 'productId' => $model->id]); ?>
                    <a href="<?= $addFavoriteProductUrl ?>" class="btn btn-sm btn-warning pull-right">
                        <i class="glyphicon glyphicon-star"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>