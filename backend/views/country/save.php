<?php

use bl\cms\shop\common\entities\ProductCountry;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $country ProductCountry
 * @var $languages Language[]
 * @var $selectedLanguage Language
 *
 */

$this->title = 'Edit country';
?>

<? $form = ActiveForm::begin(['method' => 'post']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Country'?>
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
                                            'countryId' => $country->id,
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

                    
                <?= $form->field($countryTranslation, 'title', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->label('Title')
                ?>
                </div>

                <input type="submit" class="btn btn-primary pull-right" value="<?= 'Save' ?>">

            </div>
    </div>
</div>


<? ActiveForm::end(); ?>
