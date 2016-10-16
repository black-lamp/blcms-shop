<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $countries ProductCountry
 */
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\widgets\ManageButtons;
use bl\multilang\entities\Language;
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
                    <?php if (!empty($countries)): ?>
                        <thead>
                        <tr>
                            <th class="col-md-2"><?= \Yii::t('shop', 'Id'); ?></th>
                            <th class="col-md-8"><?= \Yii::t('shop', 'Title'); ?></th>
                            <th class="col-md-2"><?= \Yii::t('shop', 'Manage'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($countries as $country) : ?>
                            <tr>
                                <td>
                                    <?= $country->id; ?>
                                </td>
                                <td>
                                    <?= $country->translation->title; ?>
                                </td>
                                <td>
                                    <?= ManageButtons::widget(['model' => $country]); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    <?php endif; ?>
                </table>

                <a href="<?= Url::to(['/shop/country/save', 'languageId' => Language::getCurrent()->id]) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>
