<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\common\entities\Product;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>


<table class="table table-hover">
    <tr>
        <th class="col-md-1 text-center">Id</th>
        <th class="col-md-7 text-center">Title</th>
        <th class="col-md-5 text-center">Price</th>
    </tr>
    <?php foreach ($products as $product) : ?>
        <tr>
            <td class="text-center">
                <?= $product->id; ?>
            </td>
            <td class="text-center">
                <?= $product->translation->title; ?>
            </td>
            <td class="text-center">
                <?= $product->price; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>



<!--GET ORDER-->
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'action' => ['/shop/cart/make-order']
]); ?>

<?= $form->field($order, 'first_name')->textInput()->label(\Yii::t('shop', 'First name')); ?>
<?= $form->field($order, 'last_name')->textInput()->label(\Yii::t('shop', 'Last name')); ?>
<?= $form->field($order, 'email')->textInput()->label(\Yii::t('shop', 'E-mail')); ?>
<?= $form->field($order, 'phone')->textInput()->label(\Yii::t('shop', 'Phone number')); ?>
<?= $form->field($order, 'address')->textInput()->label(\Yii::t('shop', 'Address')); ?>

<?= Html::submitButton(Yii::t('shop', 'Submit'), [
    'class' => ''
]); ?>

<?php $form::end(); ?>





<?php //if (!empty($dataProvider) && !empty($searchModel)) : ?>
<?//= GridView::widget([
//    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
//    'columns' => [
//        ['class' => 'yii\grid\SerialColumn'],
//
//        [
//            'label' => 'title',
//            'format' => 'raw',
//            'value' => function ($model) {
//                return Html::a($model->product->translation->title, Url::to(['/shop/' . $model->product->category->translation->seoUrl . '/' . $model->product->translation->seoUrl]));
//            }
//        ],
//        'count',
//
//        /*ACTIONS*/
//        [
//            'headerOptions' => ['class' => 'text-center col-md-2'],
//            'attribute' => \Yii::t('shop', 'Delete'),
//
//            'value' => function ($model) {
//
//                return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['remove', 'id' => $model->id]),
//                    ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right pjax']);
//
//            },
//            'format' => 'raw',
//            'contentOptions' => ['class' => 'col-md-2 text-center'],
//        ],
//    ],
//]); ?>
