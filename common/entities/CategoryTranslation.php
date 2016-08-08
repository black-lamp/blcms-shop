<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use yii\db\ActiveRecord;
/**
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $language_id
 * @property string $title
 * @property string $description
 *
 * @property Category $category
 * @property Language $language
 */
class CategoryTranslation extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'seoData' => [
                'class' => SeoDataBehavior::className()
            ]
        ];
    }
    public function rules()
    {
        return [
            [['category_id', 'language_id'], 'number'],
            [['title', 'description'], 'string'],
            [['seoUrl', 'seoTitle', 'seoDescription', 'seoKeywords'], 'string']
        ];
    }
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'text' => 'Text',
        ];
    }

    public static function tableName() {
        return 'shop_category_translation';
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getLanguage() {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    public static function treeRecoursion($categoriesTree, $parentCategory = null, $name, $category_id = null)
    {
        foreach ($categoriesTree as $oneCategory) {
            if (!empty($oneCategory['childCategory'])) {
                echo sprintf('<li class="list-group-item"><input type="radio" %s name="%s" value="%s" id="%s" %s><label for="%s">%s</label>',
                    $parentCategory == $oneCategory[0]->id ? ' checked ' : '',
                    $name,
                    $oneCategory[0]->id,
                    $oneCategory[0]->id,
                    $category_id == $oneCategory[0]->id ? 'disabled' : '',
                    $oneCategory[0]->id,
                    $oneCategory[0]->translation->title
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
                    $oneCategory[0]->translation->title
                );
                echo '</li>';
            }
        }
    }
}