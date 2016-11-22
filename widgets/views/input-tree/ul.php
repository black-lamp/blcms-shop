<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $parents \yii\db\ActiveRecord
 *
 * @var $form \yii\widgets\ActiveForm
 * @var $model \yii\base\Model
 * @var $attribute string
 * @var $languageId integer
 */
use yii\bootstrap\Html;

?>

<ul class="input-tree-ul">
    <?php foreach ($parents as $object): ?>
        <?php $children = \bl\cms\shop\widgets\InputTree::findChildren($object, $object->id); ?>
        <li>
            <?= (!empty($children)) ? Html::tag('p', '', ['class' => 'category-toggle fa fa-plus']) : ''; ?>
            <?= $form->field($model, $attribute,
                ['template' => "{input}\n{label}"]
            )->input('radio', [
                ($object->id == $model->$attribute ? 'checked' : '') =>
                    ($object->id == $model->$attribute ? 'checked' : ''),
                'value' => $object->id,
                'class' => 'radio',
                'id' => $model::className() . '-category_id-' . $object->id,
            ])->label($object->translations[$languageId]->title ?? $object->translation->title, [
                'for' => $model::className() . '-category_id-' . $object->id,
            ]); ?>

            <?= $this->render(
                '@vendor/black-lamp/blcms-shop/widgets/views/input-tree/ul',
                [
                    'parents' => $children,
                    'form' => $form,
                    'model' => $model,
                    'attribute' => $attribute,
                    'languageId' => $languageId
                ]);
            ?>
        </li>
    <?php endforeach; ?>
</ul>