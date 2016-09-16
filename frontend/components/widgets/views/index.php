<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\common\entities\Filter;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<h3><?=\Yii::t('shop', 'Filtering') ?></h3>

<?php $form = ActiveForm::begin([
    'action' => [
        '/shop/category/show',
        'id' => $category->id
    ],
    'method' => 'get',
    'options' => ['data-pjax' => true]
]);
?>

<?php foreach ($filters as $filter) : ?>
    <?php
    $newObject = Filter::getCategoryFilterValues($filter, $category->id);
    $inputType = $filter->inputType->type;
    ?>
    <?= $form->field($searchModel, $filter->type->column)
        ->$inputType(ArrayHelper::map($newObject, 'id', $filter->type->displaying_column),
            ['prompt' => '', 'name' => $filter->type->column])->label(\Yii::t('shop', $filter->type->title)) ?>
<?php endforeach; ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('shop', 'Filter'), ['class' => 'pjax btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
