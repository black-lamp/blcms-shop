<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use yii\widgets\ActiveForm;

?>

<? $addForm = ActiveForm::begin(['method' => 'post']) ?>

    <h2><?= \Yii::t('shop', 'SEO options'); ?></h2>
<?= $addForm->field($category_translation, 'seoUrl', [
    'inputOptions' => [
        'class' => 'form-control'
    ]
])->label('SEO URL')
?>
<?= $addForm->field($category_translation, 'seoTitle', [
    'inputOptions' => [
        'class' => 'form-control'
    ]
])->label(\Yii::t('shop', 'SEO title'))
?>
<?= $addForm->field($category_translation, 'seoDescription')->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO description'));
?>
<?= $addForm->field($category_translation, 'seoKeywords')->textarea(['rows' => 3])->label(\Yii::t('shop', 'SEO keywords'))
?>

<input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

<? $addForm::end(); ?>