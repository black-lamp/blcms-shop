<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 *
 * @var \yii\web\View $this
 * @var array $sortMethods
 * @var string $currentSort
 */

$moduleId = Yii::$app->controller->module->uniqueId;
?>
<div class="pull-right">
    <small class="text-center"><?= Yii::t('shop', 'Sorting') ?></small>
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="productSortDropdown" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="true">
            <?= ArrayHelper::getValue($sortMethods, $currentSort) ?>
            <span class="caret"></span>
        </button>
        <?php if (!empty($sortMethods)): ?>
            <ul class="dropdown-menu" aria-labelledby="productSortDropdown">
                <?php foreach ($sortMethods as $sort => $sortName): ?>
                    <?php if ($sort != $currentSort): ?>
                        <li>
                            <?= Html::a($sortName, ["/{$moduleId}/", 'sort' => $sort]) ?>
                        </li>
                    <?php endif ?>
                <?php endforeach; ?>
            </ul>
        <?php endif ?>
    </div>
</div>
