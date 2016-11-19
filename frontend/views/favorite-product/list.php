<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\shop\common\entities\SearchFavoriteProduct
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use bl\cms\shop\common\entities\Category;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('shop', 'Favorite products');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'project-list'
        ],
        'summary' => "",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*ARTICULUS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-1'],
                'attribute' => 'product.articulus',
                'value' => function ($model) {
                    if (!empty($model->product->articulus)) {
                        return $model->product->articulus;
                    }
                    return '';
                },
                'label' => Yii::t('shop', 'Articulus'),
                'format' => 'html',
                'contentOptions' => ['class' => 'project-title'],
            ],

            /*TITLE AND INFO*/
            [
                'headerOptions' => ['class' => 'text-center col-md-6'],
                'attribute' => 'product.translation.title',
                'value' => function ($model) {
                    if (!empty($model->product->translation->title)) {
                        $content = (!empty($model->product->translation->title)) ?
                            Html::a(
                                Html::tag('h3', $model->product->translation->title),
                                Url::toRoute(['/shop/product/show',
                                    'id' => $model->product->id, 'languageId' => Language::getCurrent()->id]),
                                ['class' => "text-success"]) : '';
                        $content .= (!empty($model->product->category->translation->title)) ?
                            Html::tag('p',
                                Html::tag('small', Yii::t('shop', 'Category') . ': ' .
                                    Html::a(
                                        $model->product->category->translation->title,
                                        Url::toRoute(['/shop/category/show',
                                            'id' => $model->product->category_id, 'languageId' => Language::getCurrent()->id]),
                                        ['class' => 'text-info']
                                    )),
                                ['class' => 'text-info'])
                            : '';

                        $content .= (!empty($model->product->translation->description)) ?
                            $model->product->translation->description : '';
                        return $content;
                    }
                    return '';
                },
                'label' => Yii::t('shop', 'Title'),
                'format' => 'html',
            ],

            /*IMAGE*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'attribute' => 'image',
                'value' => function ($model) {

                    $imageUrl = (!empty($model->product->image)) ? $model->product->image->small :
                        '/images/default.jpg';

                    return Html::a(
                        Html::img(Url::toRoute([$imageUrl])),
                        Url::toRoute(['/shop/product/show',
                            'id' => $model->product->id, 'languageId' => Language::getCurrent()->id]));

                },
                'label' => Yii::t('shop', 'Image'),
                'format' => 'html',
                'contentOptions' => ['class' => 'col-md-4 text-center'],
            ],

            /*BUTTONS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-3'],
                'value' => function ($model) {
                    $deleteButton = Html::a(
                        Yii::t('shop', 'Remove from favorites'),
                        Url::to(['/shop/favorite-product/remove', 'productId' => $model->product_id]),
                        ['class' => 'btn btn-warning col-md-12 m-t-xs']);
                    $goToButton = Html::a(
                        Yii::t('shop', 'Go to product'),
                        Url::to(['/shop/product/show', 'id' => $model->product_id]),
                        ['class' => 'btn btn-primary col-md-12']);
                    return $goToButton . $deleteButton;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ]

        ],
    ]); ?>

</div>
