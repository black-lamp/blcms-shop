<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $availabilities bl\cms\shop\common\entities\ProductAvailability
 */

use bl\cms\shop\widgets\ManageButtons;
use bl\multilang\entities\Language;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = Yii::t('shop', 'Product availabilities');
?>

<div class="panel panel-default">

    <div class="panel-heading">
        <i class="glyphicon glyphicon-list"></i>
        <?= Html::encode($this->title) ?>
    </div>

    <div class="panel-body">

        <p>
            <?= Html::a(Yii::t('shop', 'Add'), ['save'], ['class' => 'btn btn-primary btn-xs pull-right']) ?>
        </p>

        <?php if (!empty($availabilities)) : ?>
            <table class="table table-hover">
                <tr>
                    <th class="col-lg-7"><?= \Yii::t('shop', 'Title'); ?></th>
                    <th class="col-lg-2"><?= \Yii::t('shop', 'Language'); ?></th>
                    <th class="col-lg-1"><?= \Yii::t('shop', 'Edit'); ?></th>
                    <th class="col-lg-2"><?= \Yii::t('shop', 'Delete'); ?></th>
                </tr>
                <?php foreach ($availabilities as $availability) : ?>
                    <tr>
                        <td class="project-title">
                            <a href="<?= Url::toRoute(['save', 'id' => $availability->id]); ?>">
                                <?= $availability->translation->title; ?>
                            </a>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>

                        <td>
                            <?= ManageButtons::widget(['model' => $availability]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <p>
            <?= Html::a(Yii::t('shop', 'Add'), ['save'], ['class' => 'btn btn-primary btn-xs pull-right']) ?>
        </p>

    </div>

