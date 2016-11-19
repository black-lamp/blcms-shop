<?php
namespace bl\cms\shop\widgets;

use bl\cms\shop\common\entities\Category;
use bl\multilang\entities\Language;
use yii\base\Widget;

/**
 * This widget is for selecting parent category in admin panel.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategorySelector extends Widget
{
    public $categoriesTree;
    public $parentId;
    public $inputName = 'Category[parent_id]';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $categoriesWithoutParent = Category::find()->with('translations')->where(['parent_id' => null])->all();
        $categoriesTree = self::findChildren($categoriesWithoutParent);

        return $this->render('category-selector', [
            'categoriesTree' => $categoriesTree,
            'parentId' => $this->parentId,
            'inputName' => $this->inputName,
        ]);
    }

    private static function getLanguageIndex() {
        $languages = Language::find()->asArray()->all();

        foreach ($languages as $key => $language) {
            if ($language['id'] == $_GET['languageId']) {
                return $key;
            }
        }

        return '1';
    }
    private static function findChildren($parentCategories)
    {
        $tree = [];
        foreach ($parentCategories as $childCategory) {
            $childs = Category::find()->with('translations')
                ->where(['parent_id' => $childCategory->id])->all();
            $tree[] = [$childCategory, 'childCategory' => self::findChildren($childs)];
        }
        return $tree;
    }

    public static function treeRecoursion($categoriesTree, $parentCategory = null, $name, $category_id = null)
    {
        $languageIndex = self::getLanguageIndex();

        foreach ($categoriesTree as $oneCategory) {
            if (!empty($oneCategory['childCategory'])) {
                echo sprintf('<li class="list-group-item"><input type="radio" %s name="%s" value="%s" id="%s" %s><label for="%s">%s</label>',
                    $parentCategory == $oneCategory[0]->id ? ' checked ' : '',
                    $name,
                    $oneCategory[0]->id,
                    $oneCategory[0]->id,
                    $category_id == $oneCategory[0]->id ? 'disabled' : '',
                    $oneCategory[0]->id,
                    (!empty($oneCategory[0]->translations[$languageIndex])) ?
                        $oneCategory[0]->translations[$languageIndex]->title : $oneCategory[0]->translation->title
                );
                echo '<ul class="list-group">';
                self::treeRecoursion($oneCategory['childCategory'], $parentCategory, $name, $category_id);
                echo '</ul></li>';
            } else {
                echo sprintf('<li class="list-group-item"><input type="radio" %s name="%s" value="%s" id="%s" %s><label for="%s">%s</label>',
                    $parentCategory == $oneCategory[0]->id ? ' checked ' : '',
                    $name,
                    $oneCategory[0]->id,
                    $oneCategory[0]->id,
                    $category_id == $oneCategory[0]->id ? 'disabled' : '',
                    $oneCategory[0]->id,
                    (!empty($oneCategory[0]->translations[$languageIndex]->title)) ? $oneCategory[0]->translations[$languageIndex]->title : ''
                );
                echo '</li>';
            }
        }
    }
}