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
//die(var_dump($model));
?>

<div id="input-tree" data-current-category="<?=$model->$attribute ?? 0; ?>">
    <?= $this->render(
        '@vendor/black-lamp/blcms-shop/widgets/views/input-tree/ul',
        [
            'parents' => $parents,
            'form' => $form,
            'model' => $model,
            'attribute' => $attribute,
            'languageId' => $languageId
        ]);
    ?>
</div>