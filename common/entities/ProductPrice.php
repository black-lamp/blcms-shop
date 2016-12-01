<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\behaviors\TranslationBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "shop_product_price".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $price
 * @property integer $salePrice
 * @property integer $sale
 * @property integer $sale_type_id
 *
 * @property SaleType $type
 * @property Product $product
 * @property ProductPriceTranslation[] $translations
 * @property ProductPriceTranslation $translation
 */
class ProductPrice extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductPriceTranslation::className(),
                'relationColumn' => 'price_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'price'], 'required'],
            [['price', 'sale'], 'double'],
            [['product_id', 'sale_type_id'], 'integer'],
            [['sale_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleType::className(), 'targetAttribute' => ['sale_type_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'sale' => 'Discount',
            'sale_type_id' => 'Sale Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(SaleType::className(), ['id' => 'sale_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductPriceTranslation::className(), ['price_id' => 'id']);
    }

    /**
     * @return float|int
     * Gets price
     */
    public function getPrice() {
        $price = $this->price;
        if (\Yii::$app->controller->module->enableCurrencyConversion) {
            $price = floor($price * Currency::currentCurrency());
        }
        return $price;
    }

    /**
     * @return float|int
     * Gets discount price
     */
    public function getSalePrice() {
        $price = $this->price;

        if(!empty($this->sale)) {
            if(!empty($this->type)) {
                if($this->type->title == "money") {
                    $price = $this->price - $this->sale;
                }
                else if($this->type->title == "percent") {
                    $price = $this->price - ($this->price / 100) * $this->sale;
                }
            }
        }

        if (\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = floor($price * Currency::currentCurrency());
        }

        return $price;
    }
}
