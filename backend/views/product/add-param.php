<?php
/**
 * Created by Albert Gainutdinov
 *
 *@var $param Param
 *@var $languages Language[]
 *@var $selectedLanguage Language[]
 *@var $products Product
 *@var $productId Product
 */
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\Product;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Edit param';

?>

<div class="row wrapper wrapper-content animated fadeInRight">

<? $form = ActiveForm::begin(['method'=>'post']) ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="glyphicon glyphicon-edit"></i>
                    <?= 'Param' ?>
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
                                                'add-param',
                                                'id' => $param->id,
                                                'productId' => $productId,
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
                        <div class="help-block"></div>
                    </div>
                    <label for="param-product_id">Product</label>

                    <select id="param-product_id" class="form-control" name="Param[product_id]">
                        <option value="">-- <?= 'Empty' ?> --</option>
                        <? if(!empty($products)): ?>
                            <? foreach($products as $product): ?>
                                <option <?= $productId == $product->id ? 'selected' : '' ?> value="<?= $product->id?>">
                                    <?= $product->getTranslation($selectedLanguage->id)->title ?>
                                </option>
                            <? endforeach; ?>
                        <? endif; ?>
                    </select>

                    <?= $form->field($param_translation, 'name', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Name')
                    ?>
                    <?= $form->field($param_translation, 'value', [
                        'inputOptions' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Value')
                    ?>

                    <input type="submit" class="btn btn-primary pull-right" value="<?= 'Save' ?>">
                </div>
            </div>
        </div>
    </div>
<? ActiveForm::end(); ?>
</div>
