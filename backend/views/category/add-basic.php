<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\common\entities\CategoryTranslation;
use marqu3s\summernote\Summernote;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<? $addForm = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>

    <div role="tabpanel" class="tab-pane active" id="basic">
        <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>
        <!-- LANGUAGES -->
        <? if (count($languages) > 1): ?>
            <div class="dropdown">
                <button class="btn btn-warning btn-xs dropdown-toggle" type="button"
                        id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true">
                    <?= $selectedLanguage->name ?>
                    <span class="caret"></span>
                </button>
                <? if (count($languages) > 1): ?>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <? foreach ($languages as $language): ?>
                            <li>
                                <a href="
                                        <?= Url::to([
                                    'save',
                                    'categoryId' => $category->id,
                                    'languageId' => $language->id]) ?>
                                                ">
                                    <?= $language->name ?>
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                <? endif; ?>
            </div>
        <? endif; ?>

        <!-- NAME -->
        <?= $addForm->field($category_translation, 'title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->label(\Yii::t('shop', 'Name'))
        ?>

        <!-- SHOW -->
        <?= $addForm->field($category, 'show', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->checkbox(['class' => 'i-checks', 'checked ' => ($category->show) ? '' : false])
        ?>

        <!-- PARENT CATEGORY -->
        <b><?= \Yii::t('shop', 'Parent category'); ?></b>
        <?= '<ul class="list-group ul-treefree ul-dropfree">'; ?>
        <?= '<li class="list-group-item"><input type="radio" checked name="Category[parent_id]" value="" id="null"><label for="null">' . \Yii::t("shop", "Without parent") . '</label>'; ?>
        <?= CategoryTranslation::treeRecoursion($categoriesTree, $category->parent_id, 'Category[parent_id]', $category_translation->category_id); ?>
        <?= '</ul>'; ?>

        <!-- DESCRIPTION -->
        <?= $addForm->field($category_translation, 'description', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->widget(Summernote::className())->label(\Yii::t('shop', 'Description'))
        ?>

        <!-- SORT ORDER -->
        <?= $addForm->field($category, 'position', [
            'inputOptions' => [
                'class' => 'form-control'
            ]])->textInput([
            'type' => 'number',
            'max' => $maxPosition,
            'min' => $minPosition
        ]); ?>

    </div>

    <input type="submit" class="btn btn-primary pull-right" value="<?= \Yii::t('shop', 'Save'); ?>">

<? $addForm::end(); ?>