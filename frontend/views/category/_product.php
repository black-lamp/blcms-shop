<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $model \bl\cms\shop\common\entities\Product
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
            'h4',
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
    Html::dropDownList('price_id', '', ArrayHelper::map($model->prices, 'id', function ($model) {
        $priceItem = $model->translation->title . ' - ' . \Yii::$app->formatter->asCurrency($model->price);
        return $priceItem;
    })) :
    \Yii::$app->formatter->asCurrency($model->price);


$submitButton = Html::submitButton(Yii::t('shop', 'Add to cart'),
    ['class' => 'btn btn-tight btn-primary']);

$availability = (!empty($model->productAvailability)) ?
    Html::tag('div', $model->productAvailability->translation->title) : '';


echo Html::tag('div', $image, ['class' => 'col-md-3']) .
    Html::tag('div', $title . $description . $articulus . $price . $submitButton . $availability, ['class' => 'col-md-9']);
