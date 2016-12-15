<?php
namespace bl\cms\shop\widgets;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\widgets\assets\TreeWidgetAsset;
use bl\multilang\entities\Language;
use Yii;
use yii\base\Widget;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget adds tree menu using shop categories.
 * On one page may be only one Tree widget.
 *
 * Example:
 * <?= TreeWidget::widget([
 *  'className' => Category::className(),
 *  'currentCategoryId' => $category->id
 * ]); ?>
 *
 */
class TreeWidget extends Widget
{
    public $className;

    public $currentCategoryId;

    /**
     * Sets css-class for span tag when category is closed
     * @var string
     */
    public $upIconClass = 'glyphicon glyphicon-minus';

    /**
     * Sets css-class for span tag when category is opened
     * @var string
     */
    public $downIconClass = 'glyphicon glyphicon-plus';

    public function init()
    {
        TreeWidgetAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        $currentLanguage = Language::getCurrent();
        $defaultLanguage = Language::getDefault();
        if ($currentLanguage->id != $defaultLanguage->id) {
            $langId = $currentLanguage->lang_id;
        }

        if (!empty($this->className)) {
            $class = \Yii::createObject($this->className);
            $categories = $class::find()->where(['parent_id' => null, 'show' => 1])->orderBy('position')->all();

            $currentCategoryId = '';

            if (Yii::$app->controller->module->id == 'shop') {
                if (Yii::$app->controller->id == 'category') {
                    $currentCategoryId = \Yii::$app->request->get('id');
                } elseif (Yii::$app->controller->id == 'product') {
                    $product = Product::findOne(\Yii::$app->request->get('id'));
                    $currentCategoryId = $product->category_id;
                }
            }

            return $this->render('tree/tree', [
                'categories' => $categories,
                'currentCategoryId' => $currentCategoryId,
                'level' => 0,
                'context' => $this,
                'upIconClass' => $this->upIconClass,
                'downIconClass' => $this->downIconClass,
                'langId' => $langId ?? ''
            ]);
        } else return false;

    }

    public static function isOpened($categoryId, $currentCategoryId)
    {
        if (!empty($categoryId) && !empty($currentCategoryId)) {
            $parentCategoriesArray = self::findAllAncestry($currentCategoryId);

            if (in_array($categoryId, $parentCategoriesArray)) {
                return 'true';
            } else return 'false';
        } else return $categoryId;

    }

    private static function findAllAncestry($categoryId, $parentCategoriesArray = [])
    {
        $category = Category::findOne($categoryId);
        $parentCategoryId = $category->parent_id;

        if (!empty($parentCategoryId)) {
            $parentCategoriesArray[] = $parentCategoryId;
            return self::findAllAncestry($parentCategoryId, $parentCategoriesArray);
        } else return $parentCategoriesArray;
    }

}