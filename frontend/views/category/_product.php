<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $model \bl\cms\shop\common\entities\Product
 */

use bl\cms\cart\models\CartForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => ['/cart/cart/add'],
    'options' => [
        '_fields' => [
            'class' => 'col-md-4'
        ]
    ]
]);
$cart = new CartForm();


$link = Url::toRoute(['/shop/product/show', 'id' => $model->id]);

$image = (!empty($model->image)) ?
    Html::a(
        Html::img($model->image->small, [
            'class' => 'media-object img-responsive',
            'alt' => Html::encode($model->image->alt)
        ]),
        $link) :
    '';

$title = (!empty($model->translation->title)) ?
    Html::a(
        Html::tag(
            'h3',
            Html::encode($model->translation->title),
            ['class' => 'media-heading']),
        $link, ['class' => 'text-center']) : '';

$description = (!empty($model->translation->description)) ?
    Html::tag(
        'div',
        Html::encode($model->translation->description),
        ['class' => 'description']) :
    '';

$articulus = (!empty($model->articulus)) ?
    Html::tag('div',
        \Yii::t('shop', 'Articulus') . ': ' . $model->articulus,
        ['class' => 'code']) :
    '';

$price = (!empty($model->prices)) ?
    $form->field($cart, 'priceId', ['options' => ['class' => 'col-md-4']])->dropDownList(ArrayHelper::map($model->prices, 'id', function ($model) {
        $priceItem = $model->translation->title . ' - ' . \Yii::$app->formatter->asCurrency($model->price);
        return $priceItem;
    }))->label(\Yii::t('shop', 'Price')) : \Yii::$app->formatter->asCurrency($model->price);
$count = $form->field($cart, 'count', ['options' => ['class' => 'col-md-4']])->
    textInput(['type' => 'number', 'min' => 1, 'value' => 1])->label(\Yii::t('shop', 'Count'));
$productId = $form->field($cart, 'productId')->hiddenInput(['value' => $model->id])->label(false);
$submitButton = Html::submitButton(Yii::t('shop', 'Add to cart'),
    ['class' => 'btn btn-tight btn-primary']);

/*ADD TO FAVORITE*/
if (!Yii::$app->user->isGuest) {
    $addToFavButton = (!$model->isFavorite()) ? Html::a(
        Yii::t('shop', 'Add to favorites'),
        Url::to(['/shop/favorite-product/add', 'productId' => $model->id]),
        ['class' => 'btn btn-primary']
    ) :
        Html::a(
            Yii::t('shop', 'Remove from favorites'),
            Url::to(['/shop/favorite-product/remove', 'productId' => $model->id]),
            ['class' => 'btn btn-warning']
        );
}
else $addToFavButton = '';


$availability = (!empty($model->productAvailability)) ?
    Html::tag('div', $model->productAvailability->translation->title, ['class' => 'col-md-12']) : '';


echo Html::tag('div', $image, ['class' => 'col-md-3']) .
    Html::tag('div', $title . $description . $articulus . $price . $count . $productId . $submitButton . $addToFavButton . $availability, ['class' => 'col-md-9']);

$form::end();

