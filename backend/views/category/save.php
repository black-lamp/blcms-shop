<?php
use common\modules\multishop\common\entities\Category;
use common\modules\multishop\common\entities\CategoryTranslation;
use dosamigos\tinymce\TinyMce;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $category Category */
/* @var $category_translation CategoryTranslation */
/* @var $languages Language[] */
/* @var $selectedLanguage Language */
/* @var $categories Category[] */

$this->title = 'Edit category';
?>

<? $addForm = ActiveForm::begin(['action' => Url::to(['/multishop/category/save', 'categoryId' => $item->id, 'languageId' => $selectedLanguage->id]), 'method'=>'post']) ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Category'?>
            </div>
            <div class="panel-body">
                <? if(count($languages) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <?= $selectedLanguage->name ?>
                            <span class="caret"></span>
                        </button>
                        <? if(count($languages) > 1): ?>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <? foreach($languages as $language): ?>
                                    <li>
                                        <a href="
                                            <?= Url::to([
                                            'save',
                                            'categoryId' => $item->id,
                                            'languageId' => $language->id])?>
                                            ">
                                            <?= $language->name?>
                                        </a>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        <? endif; ?>
                    </div>
                <? endif; ?>
                <div class="form-group field-toolscategoryform-parent has-success">
                    <label class="control-label" for="toolscategoryform-parent"><?= 'Parent' ?></label>
                    <select id="category-parent_id" class="form-control" name="Category[parent_id]">
                        <option value="">-- <?= 'Empty' ?> --</option>
                        <? if(!empty($category)): ?>
                            <? foreach($category as $cat): ?>
                                <option <?= $item->parent_id == $cat->id ? 'selected' : '' ?> value="<?= $item->parent_id?>">
                                    <?= $cat->getTranslation($selectedLanguage->id)->title ?>
                                </option>
                            <? endforeach; ?>
                        <? endif; ?>
                    </select>
                    <div class="help-block"></div>
                </div>
                <?= $addForm->field($category_translation, 'title', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label('Title')
                ?>

                <?= $addForm->field($category_translation, 'description', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->widget(TinyMce::className(), [
                    'options' => ['rows' => 20],
                    'language' => 'ru',
                    'clientOptions' => [
                        'plugins' => [
                            'textcolor colorpicker',
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste",
                            'image'
                        ],
                        'toolbar' => "undo redo | forecolor backcolor | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    ]
                ])->label('Full description')
                ?>
                <input type="submit" class="btn btn-primary pull-right" value="<?= 'Save' ?>">
            </div>
        </div>
    </div>
</div>


<? ActiveForm::end(); ?>
