<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $countries ProductCountry
 */
use bl\cms\shop\common\entities\ProductCountry;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = 'Countries list';
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Countries list' ?>
            </div>
            <div class="panel-body">

                <table class="table table-hover">
                    <? if (!empty($countries)): ?>
                        <thead>
                        <tr>
                            <th class="col-lg-7"><?= 'Title' ?></th>
                            <? if(count($languages) > 1): ?>
                                <th class="col-lg-3"><?= 'Language' ?></th>
                            <? endif; ?>
                            <th class="col-lg-1">Edit</th>
                            <th class="col-lg-1">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($countries as $country) : ?>
                        <tr>
                            <td>
                                <?= $country->translation->title; ?>
                            </td>
                            <td>
                                <? if(count($languages) > 1): ?>
                                    <? $translations = ArrayHelper::index($country->translations, 'language_id') ?>
                                    <? foreach ($languages as $language): ?>
                                        <a href="<?= Url::to([
                                            'save',
                                            'countryId' => $country->id,
                                            'languageId' => $language->id
                                        ]) ?>"
                                           type="button"
                                           class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                           ?> btn-xs"><?= $language->name ?></a>
                                    <? endforeach; ?>
                                <? endif; ?>
                            </td>
                            <td>
                                <a href="<?= Url::to([
                                    'save',
                                    'countryId' => $country->id,
                                    'languageId' => $country->translation->language_id
                                ])?>" class="glyphicon glyphicon-edit text-warning btn btn-default btn-sm">
                                </a>
                            </td>

                                <td>
                                    <a href="<?= Url::to([
                                        'remove',
                                        'id' => $country->id
                                    ])?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    <? endif; ?>
                </table>

                <a href="<?= Url::to(['/shop/country/save', 'languageId' => Language::getCurrent()->id]) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>
