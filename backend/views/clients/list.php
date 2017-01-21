<?php
use bl\cms\shop\common\entities\Clients;
use yii\bootstrap\Html;
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var Clients $clients
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                Клиенты
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover">
                            <?php if (!empty($clients)): ?>
                                <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Remove</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td>
                                            <?= $client->email ?>
                                        </td>
                                        <td>
                                            <?= $client->phone ?>
                                        </td>

                                        <td>
                                            <?= Html::a('',
                                                ['remove', 'id' => $client->id],
                                                ['class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            <?php endif; ?>
                        </table>
                        <?= Html::a(
                            'Добавить клиента',
                            ['create'],
                            ['class' => 'btn btn-primary pull-left']
                        ) ?>
                        <?= Html::a(
                            'Скачать CSV',
                            ['export'],
                            ['class' => 'btn btn-primary pull-right']
                        ) ?>
                        <?= Html::a(
                            'Сделать рассылку',
                            ['delivery'],
                            ['class' => 'btn btn-primary pull-right']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>