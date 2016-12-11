<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

use bl\multilang\entities\Language;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

global $item;
$item = $model;

echo
    Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete', 'id' => $GLOBALS['item']->id]),
        ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right btn-xs pjax']) .

    Html::tag('div',
        Html::a(
            Html::tag('span', ' ' . \Yii::t('shop', 'Edit'),['class' => 'glyphicon glyphicon-pencil']),
            Url::toRoute(['save', 'id' => $GLOBALS['item']->id, "languageId" => Language::getCurrent()->id]),
            [
                'class' => 'btn btn-primary btn-xs',
            ]) .
        Html::a(
            '<span class="caret"></span>',
            Url::toRoute(['save', 'id' => $GLOBALS['item']->id, "languageId" => Language::getCurrent()->id]),
            [
                'class' => 'btn btn-primary btn-xs dropdown-toggle',
                'type' => 'button', 'id' => 'dropdownMenu1',
                'data-toggle' => 'dropdown', 'aria-haspopup' => 'true',
                'aria-expanded' => 'true'
            ]) .
        Html::ul(
            ArrayHelper::map(Language::find()->all(), 'id', 'name'),
            [
                'item' => function ($item, $index) {

                    return Html::tag('li',
                        Html::a(
                            $item, Url::toRoute(['save', 'id' => $GLOBALS['item']->id, "languageId" => $index]))
                    );
                },
                'class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1']),

        ['class' => 'btn-group pull-right m-r-xs']
    );