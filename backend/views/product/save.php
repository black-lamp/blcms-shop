<?php
use bl\cms\shop\common\entities\ParamsTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\Category;
use bl\multilang\entities\Language;
use dosamigos\tinymce\TinyMce;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $languages Language[] */
/* @var $selectedLanguage Language */
/* @var $product Product */
/* @var $products_translation ProductTranslation */
/* @var $params_translation ParamsTranslation */
/* @var $categories Category[] */

$this->title = 'Edit product';
?>

<? $form = ActiveForm::begin(['method'=>'post']) ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Product' ?>
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
                                            'productId' => $product->id,
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
                <div class="form-group field-validarticleform-category_id required has-success">
                    <label class="control-label" for="validarticleform-category_id"><?= 'Category' ?></label>
                    <select id="product-category_id" class="form-control" name="Product[category_id]">
                        <option value="">-- <?= 'Empty' ?> --</option>
                        <? if(!empty($category)): ?>
                            <? foreach($category as $oneCategory): ?>
                                <option <?= $product->category_id == $oneCategory->id ? 'selected' : '' ?> value="<?= $oneCategory->id?>">
                                    <?= $oneCategory->getTranslation($selectedLanguage->id)->title ?>
                                </option>
                            <? endforeach; ?>
                        <? endif; ?>
                    </select>
                    <div class="help-block"></div>
                </div>
                <?= $form->field($products_translation, 'title', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label('Title')
                ?>


                <?= $form->field($products_translation, 'description', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->widget(TinyMce::className(), [
                    'options' => ['rows' => 20],
                    'language' => 'ru',
                    'clientOptions' => [
                        'relative_urls' => false,
                        'plugins' => [
                            'textcolor colorpicker',
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste",
                            'image'
                        ],
                        'toolbar' => "undo redo | forecolor backcolor | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                    ]
                ])->label('Description')
                ?>

                <!--PARAMS-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-lg-4">
                                                Name
                                            </th>
                                            <th class="col-lg-4">
                                                Value
                                            </th>
                                            <th class="col-lg-2">
                                                Languages
                                            </th>
                                            <th class="col-lg-2">
                                                Control
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <?php var_dump($params_translation) ?>
                                            </td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary pull-right" value="<?= 'Save' ?>">
            </div>
        </div>
    </div>
</div>

<!--<div class="row">-->
<!--    <div class="col-md-12">-->
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading">-->
<!--                <i class="glyphicon glyphicon-list"></i>-->
<!--                --><?//= 'Seo Data' ?>
<!--            </div>-->
<!--            <div class="panel-body">-->
<!--                --><?//= $form->field($product_translation, 'seoUrl', [
//                    'inputOptions' => [
//                        'class' => 'form-control'
//                    ]
//                ])->label('Seo Url')
//                ?>
<!---->
<!--                --><?//= $form->field($product_translation, 'seoTitle', [
//                    'inputOptions' => [
//                        'class' => 'form-control'
//                    ]
//                ])->label('Seo Title')
//                ?>
<!---->
<!--                --><?//= $form->field($product_translation, 'seoDescription', [
//                    'inputOptions' => [
//                        'class' => 'form-control'
//                    ]
//                ])->widget(TinyMce::className(), [
//                    'options' => ['rows' => 10],
//                    'language' => 'ru',
//                    'clientOptions' => [
//                        'plugins' => [
//                            'textcolor colorpicker',
//                            "advlist autolink lists link charmap print preview anchor",
//                            "searchreplace visualblocks code fullscreen",
//                            "insertdatetime media table contextmenu paste"
//                        ],
//                        'toolbar' => "undo redo | forecolor backcolor | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
//                    ]
//                ])->label('Seo Description')
//                ?>
<!---->
<!--                --><?//= $form->field($product_translation, 'seoKeywords', [
//                    'inputOptions' => [
//                        'class' => 'form-control'
//                    ]
//                ])->widget(TinyMce::className(), [
//                    'options' => ['rows' => 10],
//                    'language' => 'ru',
//                    'clientOptions' => [
//                        'plugins' => [
//                            'textcolor colorpicker',
//                            "advlist autolink lists link charmap print preview anchor",
//                            "searchreplace visualblocks code fullscreen",
//                            "insertdatetime media table contextmenu paste"
//                        ],
//                        'toolbar' => "undo redo | forecolor backcolor | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
//                    ]
//                ])->label('Seo Keywords')
//                ?>
<!--                <input type="submit" class="btn btn-primary pull-right" value="--><?//= 'Edit' ?><!--">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div class="row">-->
<!--    <div class="col-md-12">-->
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading">-->
<!--                <i class="glyphicon glyphicon-list"></i>-->
<!--                --><?//= 'Tech' ?>
<!--            </div>-->
<!--            <div class="panel-body">-->
<!--                --><?//= $form->field($product, 'view', [
//                    'inputOptions' => [
//                        'class' => 'form-control'
//                    ]
//                ])->label('View Name')
//                ?>
<!--                <input type="submit" class="btn btn-primary pull-right" value="--><?//= 'Edit' ?><!--">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<? ActiveForm::end(); ?>
