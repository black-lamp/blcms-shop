<?php
use bl\cms\shop\common\entities\Vendor;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Vendor[] $vendors
 */

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Vendor list' ?>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <? if (!empty($vendors)): ?>
                        <thead>
                        <tr>
                            <th><?= 'Id' ?></th>
                            <th><?= 'Title' ?></th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($vendors as $vendor): ?>
                            <tr>
                                <td>
                                    <?= $vendor->id ?>
                                </td>

                                <td>
                                    <?= $vendor->title ?>
                                </td>

                                <td>
                                    <?= Html::a('', [
                                        'save',
                                        'id' => $vendor->id
                                    ], [
                                        'class' => 'glyphicon glyphicon-edit text-warning btn btn-default btn-sm'
                                    ]); ?>
                                </td>

                                <td>
                                    <?= Html::a('', [
                                        'remove',
                                        'id' => $vendor->id
                                    ], [
                                        'class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm'
                                    ]); ?>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        </tbody>
                    <? endif; ?>
                </table>

                <a href="<?= Url::to(['save']); ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>
