<?php
namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 *  This is the model class for table "shop_category".
 *
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $position
 * @property integer $show
 * @property string $image_name
 *
 * @method PositionBehavior moveNext
 * @method PositionBehavior movePrev
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'position'], 'integer'],
            [['cover', 'thumbnail', 'menu_item'], 'string'],
            [['show'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => \Yii::t('shop', 'Parent category'),
            'position' => \Yii::t('shop', 'Position'),
            'show' => \Yii::t('shop', 'Show'),
            'image_name' => \Yii::t('shop', 'Upload image'),
        ];
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
    
    public static function findChilds($parentCategories) {
    $tree = [];
    foreach ($parentCategories as $childCategory) {
        $childs = Category::find()->where(['parent_id' => $childCategory->id])->all();
        $tree[] = [$childCategory, 'childCategory' => self::findChilds($childs)];
    }
    return $tree;
}

}