<?php
namespace bl\cms\shop\common\entities;

use bl\cms\shop\common\components\user\models\UserGroup;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_product_price".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $price_id
 *
 * @property Price $price
 * @property Price $prices
 * @property Price $priceForAllUsers
 * @property Price $priceForCurrentUserGroup
 * @property Product $product
 */
class ProductPrice extends ActiveRecord
{
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
            [['product_id', 'price_id'], 'integer'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'product_id' => Yii::t('shop', 'Product'),
            'price_id' => Yii::t('shop', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Price::className(), ['id' => 'price_id']);
    }

    public function getPriceByUserGroup($userGroupId)
    {
        $price = Price::find()->where(['id' => $this->price_id, 'user_group_id' => $userGroupId])->one();
        return $price;
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getPriceForAllUsers()
    {
        $price = Price::find()->where(['id' => $this->price_id, 'user_group_id' => UserGroup::USER_GROUP_ALL_USERS])->one();
        return $price;
    }

    /**
     * @return array|null|ActiveRecord
     */
    public function getPriceForCurrentUserGroup()
    {
        $price = Price::find()->where(['id' => $this->price_id, 'user_group_id' => \Yii::$app->user->identity->user_group_id])->one();
        return $price;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}