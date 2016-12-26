<?php
namespace bl\cms\shop\common\entities;

use bl\cms\shop\common\components\user\models\UserGroup;
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
 * @property boolean $default_combination
 * @property string $articulus
 * @property integer $user_group_id
 *
 * @property Product $product
 * @property SaleType $saleType
 * @property ProductCombinationAttribute[] $shopProductCombinationAttributes
 * @property ProductCombinationImage[] $images
 * @property UserGroup $userGroup
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
            [['product_id', 'sale_type_id', 'user_group_id'], 'integer'],
            [['price', 'sale'], 'number'],
            [['default_combination'], 'boolean'],
            [['articulus'], 'string'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['sale_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SaleType::className(), 'targetAttribute' => ['sale_type_id' => 'id']],
            [['user_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::className(), 'targetAttribute' => ['user_group_id' => 'id']],
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
            'sale' => Yii::t('shop', 'Discount'),
            'sale_type_id' => Yii::t('shop', 'Discount type'),
            'image_name' => Yii::t('shop', 'Image'),
            'default' => Yii::t('shop', 'Default'),
            'articulus' => Yii::t('shop', 'Articulus'),
            'user_group_id' => \Yii::t('shop', 'User group')
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
    public function getImages()
    {
        return $this->hasMany(ProductCombinationImage::className(), ['combination_id' => 'id']);
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
    public function getOldPrice() {
        $price = $this->price;
        if (empty($price)) $price = $this->product->price;

        if (\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = $price * Currency::currentCurrency();
        }
        if (\Yii::$app->getModule('shop')->enablePriceRounding) {
            $price = floor($price);
        }

        return $price;
    }

    /**
     * @return float
     */
    public function getSalePrice() {
        $price = $this->price;
        if (empty($price)) $price = $this->product->price;

        if(!empty($this->sale)) {
            if(!empty($this->sale_type_id)) {
                if($this->saleType->title == "money") {
                    $price = $this->price - $this->sale;
                }
            else if($this->saleType->title == "percent") {
                    $price = $this->price - ($this->price / 100) * $this->sale;

                }
            }
        }

        if (\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = $price * Currency::currentCurrency();
        }
        if (\Yii::$app->getModule('shop')->enablePriceRounding) {
            $price = floor($price);
        }

        return $price;
    }

    /**
     * Finds default combination for one product and makes it not default.
     */
    public function findDefaultCombinationAndUndefault() {
        $defaultProductCombination = ProductCombination::find()
            ->where(['product_id' => $this->product_id,'default_combination' => true])
            ->andWhere(['!=', 'id', $this->id])->one();
        if (!empty($defaultProductCombination)) {
            $defaultProductCombination->default_combination = false;
            if ($defaultProductCombination->validate()) $defaultProductCombination->save();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup()
    {
        return $this->hasOne(UserGroup::className(), ['id' => 'user_group_id']);
    }

}