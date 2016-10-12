<?php
use bl\cms\shop\backend\assets\ProductAsset;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\widgets\ManageButtons;
use bl\multilang\entities\Language;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $categories CategoryTranslation
 * @var $languages Language[]
 * @var $searchModel bl\cms\shop\common\entities\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = \Yii::t('shop', 'Product list');
ProductAsset::register($this);
?>


<div class="panel panel-default">

    <!--TITLE-->
    <div class="panel-heading">
        <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
           class="pull-right btn btn-primary btn-xs">
            <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
        </a>
        <h5>
            <i class="glyphicon glyphicon-list"></i>
            <?= \Yii::t('shop', 'Product list'); ?>
        </h5>
    </div>

    <!--CONTENT-->
    <div class="panel-body">
        <?php Pjax::begin([
            'linkSelector' => '.pjax',
            'enablePushState' => true,
            'timeout' => 10000,
        ]);
        ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterRowOptions' => ['class' => 'm-b-sm m-t-sm'],
            'options' => [
                'class' => 'project-list'
            ],
            'tableOptions' => [
                'id' => 'my-grid',
                'class' => 'table table-hover'
            ],

            'summary' => "",

            'columns' => [

                /*POSITION*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'format' => 'html',
                    'label' => Yii::t('shop', 'Position'),
                    'value' => function ($model) {
                        $buttonUp = Html::a(
                            '',
                            Url::toRoute(['up', 'id' => $model->id]),
                            [
                                'class' => 'pjax product-nav glyphicon glyphicon-arrow-up text-primary pull-left'
                            ]
                        );
                        $buttonDown = Html::a(
                            '',
                            Url::toRoute(['down', 'id' => $model->id]),
                            [
                                'class' => 'pjax product-nav glyphicon glyphicon-arrow-down text-primary pull-left'
                            ]
                        );
                        return $buttonUp . $model->position . $buttonDown;
                    },
                    'contentOptions' => ['class' => 'vote-actions col-md-1'],
                ],

                /*TITLE*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-4'],
                    'attribute' => 'title',
                    'value' => function ($model) {
                        $content = Html::a(
                            $model->translation->title,
                            Url::toRoute(['save', 'productId' => $model->id, 'languageId' => Language::getCurrent()->id])
                        );
                        $content .= '<br><small>' . Yii::t('shop', 'Created') . ' ' . $model->creation_time . '</small>';
                        return $content;
                    },
                    'label' => Yii::t('shop', 'Title'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'project-title col-md-4'],
                ],

                /*CATEGORY*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => 'category',
                    'value' => 'category.translation.title',
                    'label' => Yii::t('shop', 'Category'),
                    'format' => 'text',
                    'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'translation.title'),
                    'contentOptions' => ['class' => 'project-title col-md-2'],
                ],

                /*BASE PRICE*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'value' => 'price',
                    'label' => Yii::t('shop', 'Price'),
                    'format' => 'text',
                    'contentOptions' => ['class' => 'col-md-1 text-center'],
                ],


                /*IMAGES*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'attribute' => 'images',
                    'value' => function ($model) {
                        $content = '';
                        $number = 3;
                        $i = 0;
                        foreach ($model->images as $image) {
                            if (!empty($image)) {
                                if ($i < $number) {
                                    $content .= Html::img('/images/shop-product/' . $image->file_name . '-small.jpg', ['class' => 'img-circle']);
                                    $i++;
                                }
                            }
                        }
                        return Html::a($content, Url::toRoute(['add-image', 'productId' => $model->id, 'languageId' => Language::getCurrent()->id]));
                    },
                    'label' => Yii::t('shop', 'Images'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'col-md-1 project-people'],
                ],

                /*STATUS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'attribute' => \Yii::t('shop', 'Status'),

                    'value' => function ($model) {
                        switch ($model->status) {
                            case Product::STATUS_ON_MODERATION:
                                return Html::tag('p', \Yii::t('shop', 'On moderation'), ['class' => 'col-md-12 btn btn-warning btn-xs']);
                                break;
                            case Product::STATUS_DECLINED:
                                return Html::tag('p', \Yii::t('shop', 'Declined'), ['class' => 'col-md-12 btn btn-danger btn-xs']);
                                break;
                            case Product::STATUS_SUCCESS:
                                return Html::tag('p', \Yii::t('shop', 'Success'), ['class' => 'col-md-12 btn btn-primary btn-xs']);
                                break;
                            default:
                                return $model->status;
                        }
                    },
                    'label' => Yii::t('shop', 'Status'),
                    'format' => 'html',
                    'filter' => Html::activeDropDownList($searchModel, 'status',
                        [
                            Product::STATUS_ON_MODERATION => \Yii::t('shop', 'On moderation'),
                            Product::STATUS_DECLINED => \Yii::t('shop', 'Declined'),
                            Product::STATUS_SUCCESS => \Yii::t('shop', 'Success')
                        ], ['class' => 'form-control', 'prompt' => 'Любой статус']),
                    'contentOptions' => ['class' => 'project-title text-center col-md-1'],
                ],

                /*ACTIONS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => \Yii::t('shop', 'Manage'),

                    'value' => function ($model) {
                        return ManageButtons::widget(['model' => $model]);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-2 text-center'],
                ],
            ],
        ]);
        ?>
        <?php
        Pjax::end();
        ?>
        <?= \Yii::t('shop', 'Count of waiting moderation products is') . ' <b>' . $notModeratedProductsCount . '</b>'; ?>
    </div>
</div>