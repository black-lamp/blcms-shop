<?php

use bl\cms\shop\common\entities\Category;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel bl\cms\shop\common\entities\SearchCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('shop', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">

    <div class="panel-heading">
        <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-user-plus']) .
            Yii::t('shop', 'Create Category'), ['save', 'languageId' => Language::getCurrent()->id], ['class' => 'btn btn-primary btn-xs pull-right']);
        ?>
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= Html::encode($this->title); ?>
        </h5>
    </div>

    <div class="panel-body">
        <?php Pjax::begin([
            'enablePushState' => false,
            'timeout' => 10000,
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterRowOptions' => ['class' => ''],
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
                                'class' => 'category-nav glyphicon glyphicon-arrow-up text-primary pull-left'
                            ]
                        );
                        $buttonDown = Html::a(
                            '',
                            Url::toRoute(['down', 'id' => $model->id]),
                            [
                                'class' => 'category-nav glyphicon glyphicon-arrow-down text-primary pull-left'
                            ]
                        );
                        return $buttonUp . '<div>' . $model->position . '</div>' . $buttonDown;
                    },
                    'contentOptions' => ['class' => 'vote-actions col-md-1'],
                ],

                /*TITLE*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-3'],
//                    'attribute' => 'title',
                    'value' => function ($model) {
                        $content = null;
                        if (!empty($model->translation->title)) {
                            $content = Html::a(
                                $model->translation->title,
                                Url::toRoute(['save', 'categoryId' => $model->id, 'languageId' => Language::getCurrent()->id])
                            );
                        }
                        return $content;
                    },
                    'label' => Yii::t('shop', 'Title'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'project-title col-md-3'],
                ],

                /*PARENT CATEGORY*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-3'],
                    'attribute' => 'parent_id',
                    'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'translation.title'),
                    'value' => 'parent.translation.title',
                    'label' => Yii::t('shop', 'Parent category'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'project-title col-md-3'],
                ],

                /*IMAGES*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => 'images',
                    'value' => function ($model) {
                        $content = '';

                        if (!empty($model->cover)) {
                            $content .= Html::img('/images/shop-category/' . $model->cover . '-small.jpg', ['class' => 'img-circle']);
                        }
                        if (!empty($model->thumbnail)) {
                            $content .= Html::img('/images/shop-category/' . $model->thumbnail . '-small.jpg', ['class' => 'img-circle']);
                        }
                        if (!empty($model->menu_item)) {
                            $content .= Html::img('/images/shop-category/' . $model->menu_item . '-small.jpg', ['class' => 'img-circle']);
                        }

                        return Html::a($content, Url::toRoute(['add-image', 'categoryId' => $model->id, 'languageId' => Language::getCurrent()->id]));
                    },
                    'label' => Yii::t('shop', 'Images'),
                    'format' => 'html',
                    'contentOptions' => ['class' => 'col-md-2 project-people'],
                ],

                /*SHOW*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'format' => 'html',
                    'attribute' => 'show',
                    'filter' => [1 => \Yii::t('shop', 'On'), 0 => \Yii::t('shop', 'Off')],
                    'label' => Yii::t('shop', 'Show'),
                    'contentOptions' => ['class' => 'text-center col-md-1'],

                    'value' => function ($model) {
                        return Html::a(
                            Html::tag('i', '', ['class' => $model->show ? 'glyphicon glyphicon-ok text-primary' : 'glyphicon glyphicon-minus text-danger']),
                            Url::to([
                                'switch-show',
                                'id' => $model->id
                            ]),
                            [
                                'class' => 'category-nav'
                            ]);
                    }
                ],

                /*ACTIONS*/
                [
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'attribute' => \Yii::t('shop', 'Manage'),

                    'value' => function ($model) {
                        global $category;
                        $category = $model;

                        $languages = Language::findAll(['active' => true]);
                        $list =
                            Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete', 'id' => $GLOBALS['category']->id]),
                                ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right pjax']) .

                            Html::tag('div',
                                Html::a(
                                    'Edit',
                                    Url::toRoute(['save', 'categoryId' => $GLOBALS['category']->id, "languageId" => Language::getCurrent()->id]),
                                    [
                                        'class' => 'col-md-8 btn btn-default ',
                                    ]) .
                                Html::a(
                                    '<span class="caret"></span>',
                                    Url::toRoute(['save', 'categoryId' => $GLOBALS['category']->id, "languageId" => Language::getCurrent()->id]),
                                    [
                                        'class' => 'block col-md-4 btn btn-default dropdown-toggle',
                                        'type' => 'button', 'id' => 'dropdownMenu1',
                                        'data-toggle' => 'dropdown', 'aria-haspopup' => 'true',
                                        'aria-expanded' => 'true'
                                    ]) .
                                Html::ul(
                                    ArrayHelper::map($languages, 'id', 'name'),
                                    [
                                        'item' => function ($item, $index) {
                                            return Html::tag('li',
                                                Html::a($item, Url::toRoute(['save', 'categoryId' => $GLOBALS['category']->id, "languageId" => $index]), []),
                                                []
                                            );
                                        },
                                        'class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1']),

                                ['class' => 'btn-group pull-left']
                            );

                        return $list;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-md-2 text-center'],
                ],
            ],
        ]);
        ?>

        <?php Pjax::end(); ?>

        <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-user-plus']) .
            Yii::t('shop', 'Create Category'), ['save', 'languageId' => Language::getCurrent()->id], ['class' => 'btn btn-primary btn-xs pull-right']);
        ?>
    </div>

</div>