<?php
use bl\cms\shop\backend\assets\ProductAsset;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\SearchProduct;
use bl\cms\shop\widgets\ManageButtons;
use bl\multilang\entities\Language;
use dektrium\user\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this View
 * @var $categories CategoryTranslation
 * @var $languages Language[]
 * @var $searchModel SearchProduct
 * @var $dataProvider ActiveDataProvider
 * @var $notModeratedProductsCount Product
 */

$this->title = \Yii::t('shop', 'Product list');
ProductAsset::register($this);

$this->params['breadcrumbs'] = [
    Yii::t('shop', 'Shop'),
    Yii::t('shop', 'Products')
];
?>

<div class="ibox">

    <!--TITLE-->
    <div class="ibox-title">
        <div class="ibox-tools">
            <h5>
                <i class="glyphicon glyphicon-list"></i>
                <?= \Yii::t('shop', 'Product list'); ?>
            </h5>
            <!--ADD BUTTON-->
            <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
               class="btn btn-primary btn-xs">
                <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
            </a>
        </div>
    </div>

    <!--CONTENT-->
    <div class="ibox-content">
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
                                'class' => 'pjax fa fa-chevron-up'
                            ]
                        );
                        $buttonDown = Html::a(
                            '',
                            Url::toRoute(['down', 'id' => $model->id]),
                            [
                                'class' => 'pjax fa fa-chevron-down'
                            ]
                        );
                        return $buttonUp . '<div>' . $model->position . '</div>' . $buttonDown;
                    },
                    'contentOptions' => ['class' => 'vote-actions'],
                ],

                /*TITLE*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-4'],
                    'attribute' => 'title',
                    'value' => function ($model) {
                        $content = null;
                        if (!empty($model->translation->title)) {
                            /** @var User $owner */
                            $owner = (!empty(User::find()->where(['id' => $model->owner])->one()))
                                ? User::find()->where(['id' => $model->owner])->one()
                                : new User();

                            $content = Html::a(
                                $model->translation->title,
                                Url::toRoute(['save', 'id' => $model->id, 'languageId' => Language::getCurrent()->id])
                            );
                            $content .= '<br><small>' . Yii::t('shop', 'Created') . ' ' . $model->creation_time . '</small><br>';
                            $content .= '<small>' . \Yii::t('shop', 'Created by') . ' ' . $owner->email . '</small>';
                        }
                        return $content;
                    },
                    'label' => Yii::t('shop', 'Title'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'project-title'],
                ],

                /*CATEGORY*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => 'category',
                    'value' => 'category.translation.title',
                    'label' => Yii::t('shop', 'Category'),
                    'format' => 'text',
                    'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'translation.title'),
                    'contentOptions' => ['class' => 'project-title'],
                ],

                /*IMAGES*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => 'images',
                    'value' => function ($model) {
                        $content = '';
                        $number = 3;
                        $i = 0;
                        foreach ($model->images as $image) {
                            if (!empty($image)) {
                                if ($i < $number) {
                                    $content .= Html::img($image->small, ['class' => 'img-circle']);
                                    $i++;
                                }
                            }
                        }
                        return Html::a($content, Url::toRoute(['add-image', 'id' => $model->id, 'languageId' => Language::getCurrent()->id]));
                    },
                    'label' => Yii::t('shop', 'Images'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'project-people'],
                ],

                /*STATUS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'attribute' => \Yii::t('shop', 'Status'),

                    'value' => function ($model) {
                        switch ($model->status) {
                            case Product::STATUS_ON_MODERATION:
                                return
                                    Html::button(
                                        Yii::$app->user->can('moderateProductCreation') ?
                                        Html::a(\Yii::t('shop', 'On moderation'),
                                            Url::toRoute(['save', 'id' => $model->id, 'languageId' => Language::getCurrent()->id]),
                                            ['class' => '']) :
                                        Html::tag('span', \Yii::t('shop', 'On moderation')),
                                        ['class' => 'col-md-12 btn btn-warning btn-xs']
                                    );
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
                    'format' => 'raw',
                    'filter' => Html::activeDropDownList($searchModel, 'status',
                        [
                            Product::STATUS_ON_MODERATION => \Yii::t('shop', 'On moderation'),
                            Product::STATUS_DECLINED => \Yii::t('shop', 'Declined'),
                            Product::STATUS_SUCCESS => \Yii::t('shop', 'Success')
                        ], ['class' => 'form-control', 'prompt' => \Yii::t('shop', 'All')]),
                    'contentOptions' => ['class' => 'project-title text-center'],
                ],

                /*ACTIONS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => \Yii::t('shop', 'Control'),

                    'value' => function ($model) {
                        return ManageButtons::widget(['model' => $model]);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                ],
            ],
        ]);
        ?>
        <?php
        Pjax::end();
        ?>
        <?= \Yii::t('shop', 'Count of waiting moderation products is') . ' <b>' . $notModeratedProductsCount . '</b>'; ?>

        <!--ADD BUTTON-->
        <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
           class="btn btn-primary btn-xs pull-right">
            <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
        </a>
    </div>
</div>
