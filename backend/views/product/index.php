<?php
use bl\cms\shop\backend\assets\PjaxLoaderAsset;
use bl\cms\shop\backend\assets\ProductAsset;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\CategoryTranslation;
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

<?php Pjax::begin([
    'linkSelector' => '.pjax',
    'enablePushState' => false,
    'timeout' => 10000,
    ]);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="glyphicon glyphicon-list"></i>
        <?= \Yii::t('shop', 'Product list'); ?>
        <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
           class="text-right btn btn-primary btn-xs pull-right">
            <i class="fa fa-user-plus"></i> <?= \Yii::t('shop', 'Add'); ?>
        </a>
    </div>
    <div class="panel-body">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'id' => 'my-grid',
                'class' => 'table table-hover'
            ],

            'summary' => "",

            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                ],

                /*TITLE*/
                [
                    'attribute' => 'title',
                    'value' => 'translation.title',
                    'label' => Yii::t('shop', 'Title'),
                    'format' => 'text',
                    'headerOptions' => ['class' => 'text-center col-md-3'],
                ],

                /*CATEGORY*/
                [
                    'attribute' => 'category',
                    'value' => 'category.translation.title',
                    'label' => Yii::t('shop', 'Category'),
                    'format' => 'text',
                    'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'translation.title'),
                    'headerOptions' => ['class' => 'text-center col-md-3'],
                ],

                /*CREATION DATE*/
                [
                    'attribute' => 'creation_time',
                    'format' => ['date', 'php:d-m-Y'],
                    'label' => Yii::t('shop', 'Creation date'),
                    'headerOptions' => ['class' => 'text-center col-md-2'],

                ],

                /*LANGUAGE*/
                [
                    'attribute' => 'language',
                    'format' => 'html',
                    'label' => Yii::t('shop', 'Translations'),
                    'headerOptions' => ['class' => 'text-center col-md-2'],
                    'value' => function ($model) {
                        $languages = Language::findAll(['active' => true]);
                        if (count($languages)) {
                            $translations = ArrayHelper::index($model->translations, 'language_id');

                            $buttons = '';
                            foreach ($languages as $language) {
                                $buttons .= Html::a(
                                    $language->name,
                                    Url::toRoute(['save', 'productId' => $model->id, 'languageId' => $language->id]),
                                    [
                                        'class' => !empty($translations[$language->id]) ? 'btn btn-primary btn-xs' : 'btn btn-danger btn-xs'
                                    ]
                                );
                            }
                            return $buttons;
                        } else return false;
                    }
                ],

                /*POSITION*/
                [
                    'attribute' => 'position',
                    'format' => 'html',
                    'label' => Yii::t('shop', 'Position'),
                    'headerOptions' => ['class' => 'text-center col-md-1'],

                    'value' => function ($model) {
                        $buttonUp = Html::a(
                            '',
                            Url::toRoute(['up', 'id' => $model->id]),
                            [
                                'class' => 'pjax product-nav glyphicon glyphicon-arrow-up text-primary pull-left'
                            ]
                        );
                        $position = Html::tag('span', $model->position, ['class' => 'pull-left']);
                        $buttonDown = Html::a(
                            '',
                            Url::toRoute(['down', 'id' => $model->id]),
                            [
                                'class' => 'pjax product-nav glyphicon glyphicon-arrow-down text-primary pull-left'
                            ]
                        );
                        return $buttonUp . $position . $buttonDown;
                    }
                ],

                /*ACTION BUTTONS*/
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'headerOptions' => ['class' => 'text-center col-md-1'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['save', 'productId' => $key, 'languageId' => Language::getCurrent()->id]),
                                ['title' => Yii::t('yii', 'Update'), 'data-pjax' => '0']);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['remove', 'id' => $key]),
                                ['title' => Yii::t('yii', 'Delete'), 'class' => 'pjax']);
                        }
                    ]
                ],
            ],
        ]);
        ?>
    </div>
</div>

<?php
Pjax::end();
?>