<?php
namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii2tech\ar\position\PositionBehavior;

/**
 *  This is the model class for table "shop_category".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $position
 * @property integer $show
 * @property string $cover
 * @property string $thumbnail
 * @property string $menu_item
 *
 * @property CategoryTranslation[] $categoryTranslations
 * @property Filter[] $filters
 * @property Product[] $products
 *
 * @method PositionBehavior moveNext
 * @method PositionBehavior movePrev
 */
class Category extends ActiveRecord
{

    private static $imageCategory = 'shop-category';
    private static $image_extension = '.jpg';

    /**
     * @inheritdoc
     */
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
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'parent_id'
                ],
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
            [['show'], 'boolean'],
            [['cover', 'thumbnail', 'menu_item'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'position' => Yii::t('shop', 'Position'),
            'show' => Yii::t('shop', 'Show'),
            'cover' => Yii::t('shop', 'Cover'),
            'thumbnail' => Yii::t('shop', 'Thumbnail'),
            'menu_item' => Yii::t('shop', 'Menu Item'),
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
    public function getFilters()
    {
        return $this->hasMany(Filter::className(), ['category_id' => 'id']);
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

    public static function findChilds($parentCategories)
    {
        $tree = [];
        foreach ($parentCategories as $childCategory) {
            $childs = Category::find()->where(['parent_id' => $childCategory->id])->all();
            $tree[] = [$childCategory, 'childCategory' => self::findChilds($childs)];
        }
        return $tree;
    }

    public static function getBig($category, $imageType) {
        $fileName = $category->$imageType;
        return (Yii::getAlias('@frontend/web/images') . '/' . self::$imageCategory . '/' . $imageType . '/' . $fileName . '-big' . self::$image_extension);
    }

    public static function getThumb($category, $imageType) {
        $fileName = $category->$imageType;
        return (Yii::getAlias('@frontend/web/images/') . '/' . self::$imageCategory .  '/' . $imageType . '/' . $fileName . '-thumb' . self::$image_extension);
    }

    public static function getSmall($category, $imageType) {
        $fileName = $category->$imageType;
        return (Yii::getAlias('@frontend/web/images/') . '/' . self::$imageCategory .  '/' . $imageType . '/' . $fileName . '-small' . self::$image_extension);
    }

    public static function getOriginal($category, $imageType) {
        $fileName = $category->$imageType;
        return (Yii::getAlias('@frontend/web/images/') . '/' . self::$imageCategory .  '/' . $imageType . '/' . $fileName . '-original' . self::$image_extension);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '/' . Yii::$app->controller->module->id . '/category/show';
        return Url::to([$url, 'id' => $this->id]);
    }
}