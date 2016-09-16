<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "shop_product_price".
 *
 * @property integer $id
 * 
 */
class ProductCountry extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductCountryTranslation::className(),
                'relationColumn' => 'country_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer']
        ];
    }

    public function getTranslations()
    {
        return $this->hasMany(ProductCountryTranslation::className(), ['country_id' => 'id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['country_id' => 'id']);
    }
}
