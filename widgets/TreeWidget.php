<?php
namespace bl\cms\shop\widgets;

use bl\cms\shop\widgets\assets\TreeWidgetAsset;
use yii\base\Widget;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget adds tree menu using shop categories.
 * On one page may be only one Tree widget.
 *
 * Example:
 * <?= TreeWidget::widget(['className' => Category::className()]); ?>
 *
 */
class TreeWidget extends Widget
{
    public $className;

    public function init()
    {
        TreeWidgetAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        if (!empty($this->className)) {
            $class = \Yii::createObject($this->className);
            $categories = $class::find()->where(['parent_id' => null])->all();

            return $this->render('tree', [
                'categories' => $categories,
                'level' => 0
            ]);
        }
        else return false;

    }

}