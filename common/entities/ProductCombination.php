<?php
namespace bl\cms\shop\common\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_product_combination".
 *
 * @property integer $id
 * @property integer $product_id
 * @property double $price
 * @property double $sale
 * @property integer $sale_type_id
 * @property string $image_name
 * @property integer $default
 *
 * @property Product $product
 * @property SaleType $saleType
 * @property ProductCombinationAttribute[] $shopProductCombinationAttributes
 * @property ProductCombinationImage[] $shopProductCombinationImages
 */
class ProductCombination extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_combination';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'sale_type_id', 'default'], 'integer'],
            [['price', 'sale'], 'number'],
            [['image_name'], 'string'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['sale_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleType::className(), 'targetAttribute' => ['sale_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'product_id' => Yii::t('shop', 'Product ID'),
            'price' => Yii::t('shop', 'Price'),
            'sale' => Yii::t('shop', 'Sale'),
            'sale_type_id' => Yii::t('shop', 'Sale Type ID'),
            'image_name' => Yii::t('shop', 'Image'),
            'default' => Yii::t('shop', 'Default'),
        ];
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
    public function getSaleType()
    {
        return $this->hasOne(SaleType::className(), ['id' => 'sale_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopProductCombinationAttributes()
    {
        return $this->hasMany(ProductCombinationAttribute::className(), ['combination_id' => 'id']);
    }

    /**
     * @return float
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