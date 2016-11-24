<?php
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $param Param
 * @var $param_translation ParamTranslation
 * @var $selectedLanguage Language[]
 * @var $products Product
 * @var $id Product
 * @var $product Product
 */

?>

<?php $form = ActiveForm::begin([
    'action' => [
        'product/add-param',
        'id' => $id,
        'languageId' => $selectedLanguage->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'param',
        'data-pjax' => true
    ]
]);
?>

<br>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="text-center" colspan="4">
            <h2>
                <?= \Yii::t('shop', 'Params'); ?>
            </h2>
        </th>
    </tr>
    <tr>
        <th class="col-md-5 text-center">
            <?= \Yii::t('shop', 'Title'); ?>
        </th>
        <th class="col-md-6 text-center">
            <?= \Yii::t('shop', 'Value'); ?>
        </th>
        <th class="col-md-1 text-center">
            <?= \Yii::t('shop', 'Control'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($product->params)) : ?>
        <?php foreach ($product->params as $param) : ?>
            <tr class="text-center">
                <td>
                    <?= $param->translation->name ?>
                </td>
                <td>
                    <?= $param->translation->value ?>
                </td>
                <td>
                    <a href="<?= Url::to([
                        'delete-param',
                        'id' => $param->translation->param_id,
                    ]) ?>"
                       class="param glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
        <tr>
            <td>
                <?= $form->field($param_translation, 'name', [
                    'inputOptions' => [
                        'class' => 'form-control col-md-5'
                    ]
                ])->label(false)
                ?>
            </td>
            <td>
                <?= $form->field($param_translation, 'value', [
                    'inputOptions' => [
                        'class' => 'form-control col-md-5'
                    ]
                ])->label(false)
                ?>
            </td>
            <td>
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </tbody>
</table>

<?php $form->end(); ?>
