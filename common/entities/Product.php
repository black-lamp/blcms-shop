<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 21.05.2016
 * Time: 10:35
 */

namespace bl\cms\multishop\common\entities;

/**
 * Article
 *
 * @property integer $id
 * @property integer $category_id
 * @property boolean $show
 * @property string $view
 *
 * @property Category $category
 * @property ProductTranslation[] $translations
 * @property ProductTranslation $translation
 */
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductTranslation::className(),
                'relationColumn' => 'product_id'
            ],
        ];
    }

    public function rules()
    {
        return [
            ['category_id', 'number'],
            ['price', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
    }
}
