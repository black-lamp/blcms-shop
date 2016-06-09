<?php
namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * @author Albert Gainutdinov
 * 
 * @property integer $id
 * @property integer $parent_id
 * @property integer $category_id
 *
 * @property Product[] $products
 * @property Category $parent
 * @property Category[] $children
 * @property CategoryTranslation $translation
 * @property CategoryTranslation[] $translations
 */
class Category extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => CategoryTranslation::className(),
                'relationColumn' => 'category_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position'
                
            ],
        ];
    }

    public function rules()
    {
        return [
            ['parent_id', 'number']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_category';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(CategoryTranslation::className(), ['category_id' => 'id']);
    }    
}