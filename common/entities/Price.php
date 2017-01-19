<?php
namespace bl\cms\shop\common\entities;

use yii\base\Exception;
use yii\db\ActiveRecord;
use bl\cms\shop\common\components\user\models\UserGroup;

/**
 * This is the model class for table "shop_price".
 *
 * @property integer $id
 * @property integer $combination_id
 * @property integer $user_group_id
 * @property double $price
 * @property double $discount
 * @property integer $discount_type_id
 *
 * @property UserGroup $userGroup
 * @property PriceDiscountType $discountType
 * @property float $oldPrice
 * @property float $discountPrice
 */
class Price extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_group_id', 'discount_type_id', 'number'], 'integer'],
            [['user_group_id'], 'default'],
            [['price', 'discount'], 'number'],
            [['discount_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceDiscountType::className(), 'targetAttribute' => ['discount_type_id' => 'id']],
            [['user_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::className(), 'targetAttribute' => ['user_group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('shop', 'ID'),
            'user_group_id' => \Yii::t('shop', 'User Group'),
            'price' => \Yii::t('shop', 'Price'),
            'discount' => \Yii::t('shop', 'Discount'),
            'discount_type_id' => \Yii::t('shop', 'Discount Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup()
    {
        return $this->hasOne(UserGroup::className(), ['id' => 'user_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountType()
    {
        return $this->hasOne(PriceDiscountType::className(), ['id' => 'discount_type_id']);
    }

    /**
     * Gets price
     * @return float|int
     */
    public function getOldPrice()
    {
        $price = $this->price;
        if (\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = $price * Currency::currentCurrency();
        }
        if (\Yii::$app->getModule('shop')->enablePriceRounding) {
            $price = floor($price);
        }

        return $price;
    }

    /**
     * Gets price with discount
     * @return float|int
     * @throws Exception
     */
    public function getDiscountPrice()
    {
        $price = $this->price;

        if (!empty($this->discount) && !empty($this->discount_type_id)) {
            if ($this->discountType->title == "money") {
                $price = $this->price - $this->discount;
            } else if ($this->discountType->title == "percent") {
                $price = $this->price - ($this->price / 100) * $this->discount;
            } else throw new Exception(\Yii::t('shop', 'Such discount type does not exist.'));
        }

        if (\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = $price * Currency::currentCurrency();
        }
        if (\Yii::$app->getModule('shop')->enablePriceRounding) {
            $price = floor($price);
        }

        return $price;
    }

}
