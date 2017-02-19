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
 * @var $params Param[]
 * @var $param_translation ParamTranslation
 * @var $productId integer
 * @var $languageId integer
 * @var $languageIndex integer
 */
?>

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'action' => [
        'product-param/add-param',
        'id' => $productId,
        'languageId' => $languageId
    ],
    'method' => 'post',
    'options' => [
        'class' => 'param',
        'data-pjax' => true
    ]
]);
?>

<table class="table table-bordered">
    <thead>
    <tr>
        <th class="col-md-1 text-center">
            <?= \Yii::t('shop', 'Position'); ?>
        </th>
        <th class="col-md-5 text-center">
            <?= \Yii::t('shop', 'Title'); ?>
        </th>
        <th class="col-md-5 text-center">
            <?= \Yii::t('shop', 'Value'); ?>
        </th>
        <th class="col-md-1 text-center">
            <?= \Yii::t('shop', 'Control'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($params)) : ?>
        <?php foreach ($params as $param) : ?>
            <tr class="text-center">
                <td>
                    <?= Html::a(
                        '',
                        Url::toRoute(['up', 'id' => $param->id]),
                        [
                            'class' => 'fa fa-chevron-up'
                        ]
                    );?>
                    <?= $param->position; ?>
                    <?= $buttonDown = Html::a(
                        '',
                        Url::toRoute(['down', 'id' => $param->id]),
                        [
                            'class' => 'fa fa-chevron-down'
                        ]
                    );?>
                </td>
                <td>
                    <?php foreach ($param->translations as $translation) : ?>
                        <?php if ($translation->language_id == $languageId) : ?>

                            <?= $translation->name ?? '' ?>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </td>

                <td>
                    <?php foreach ($param->translations as $translation) : ?>
                        <?php if ($translation->language_id == $languageId) : ?>

                            <?= $translation->value ?? '' ?>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </td>

                <td>
                    <a href="<?= Url::to([
                        'update-param',
                        'id' => $param->id,
                        'languageId' => $languageId
                    ]) ?>"
                       class="param glyphicon glyphicon-edit text-danger btn btn-default btn-sm"></a>
                    <a href="<?= Url::to([
                        'delete-param',
                        'id' => $param->id,
                    ]) ?>"
                       class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <tr>
        <td></td>
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
<?php Pjax::end(); ?>
