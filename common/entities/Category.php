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
 * @property boolean $additional_products
 *
 * @property CategoryTranslation[] $categoryTranslations
 * @property Filter[] $filters
 * @property Product[] $products
 * @property CategoryTranslation $translation
 * @property Category $parent
 *
 * @method CategoryTranslation getTranslation($languageId = null)
 * @method PositionBehavior moveNext
 * @method PositionBehavior movePrev
 */
class Category extends ActiveRecord
{

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
            [['show', 'additional_products'], 'boolean'],
            [['cover', 'thumbnail', 'menu_item'], 'string', 'max' => 255],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'parent_id',
            'position',
//            'show',
            'cover',
            'thumbnail',
            'menu_item',
            'additional_products',
            'categoryTranslations'
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
            'additional_products' => \Yii::t('shop', 'Additional products'),
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
     * Gets shown children
     * @param $parent_id integer
     * @return \yii\db\ActiveQuery|array
     */
    public function getChildren($parent_id = null)
    {
        $parent_id = $parent_id ?? $this->id;
        $children = $this::find()
            ->where(['parent_id' => $parent_id, 'show' => true])
            ->orderBy(['position' => SORT_ASC])
            ->all();
        return $children;
    }

    /**
     * Gets all children
     * @param $parent_id integer
     * @return \yii\db\ActiveQuery|array
     */
    public function getAllChildren($parent_id = null)
    {
        $parent_id = $parent_id ?? $this->id;
        $children = $this::find()->where(['parent_id' => $parent_id])->all();
        return $children;
    }

    /**
     * @param Category $category
     * @return array
     *
     * Gets children of category and its children.
     */
    public function getDescendants($category)
    {
        $children = $category->getChildren($category->id);

        if (!empty($children)) {
            foreach ($children as $child) {

                $grandChildren = $this->getDescendants($child);
                if (!empty($grandChildren)) {

                    $children = array_merge($children, $grandChildren);
                }
            }
        }
        return $children;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(CategoryTranslation::className(), ['category_id' => 'id']);
    }

    /**
     * @param $imagableCategory
     * @param $size
     * @return mixed|string
     */
    public function getImage($imagableCategory, $size)
    {
        $imageNameProperty = str_replace('shop-category/', '', $imagableCategory);
        $imageName = $this->$imageNameProperty;
        if (!empty($imageName)) {
            $fullPath = \Yii::$app->shop_imagable
                ->get($imagableCategory, $size, $imageName);
            $path = str_replace('frontend', '', Yii::$app->basePath);
            $path = str_replace('backend', '', $path);
            return str_replace($path . 'frontend/web', '', $fullPath);

        } else return '';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '/' . Yii::$app->controller->module->id . '/category/show';
        return Url::to([$url, 'id' => $this->id]);
    }

    /**
     * Adds title, meta-description and meta-keywords to category page using bl\cms\seo\StaticPageBehavior.
     */
    public function registerMetaData()
    {
        $currentView = Yii::$app->controller->view;

        $currentView->title = html_entity_decode($this->translation->seoTitle) ??
            html_entity_decode($this->translation->title);
        $currentView->registerMetaTag([
            'name' => 'description',
            'content' => html_entity_decode($this->translation->seoDescription)
        ]);
        $currentView->registerMetaTag([
            'name' => 'keywords',
            'content' => html_entity_decode($this->translation->seoKeywords)
        ]);
    }
}