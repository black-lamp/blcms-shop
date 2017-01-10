<?php
namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use bl\cms\shop\common\components\user\models\UserGroup;

/**
 * This is the model class for table "shop_combination".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $sku
 * @property integer $default
 *
 * @property Product $product
 * @property CombinationAttribute[] $shopCombinationAttributes
 * @property CombinationImage[] $shopCombinationImages
 * @property CombinationTranslation[] $shopCombinationTranslations
 */
class Combination extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_combination';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => CombinationTranslation::className(),
                'relationColumn' => 'combination_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'default'], 'integer'],
            [['sku'], 'string', 'max' => 255],
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
            'sku' => Yii::t('shop', 'SKU'),
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
     * Gets prices for all user groups
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Price::className(), ['combination_id' => 'id']);
    }

    /**
     * Gets price for user group
     * @return array|null|ActiveRecord
     */
    public function getPrice()
    {
        if (\Yii::$app->user->isGuest) {
            $params = [
                'combination_id' => $this->id,
                'user_group_id' => UserGroup::USER_GROUP_ALL_USERS
            ];
        }
        else {
            $params = [
                'combination_id' => $this->id,
                'user_group_id' => \Yii::$app->user->identity->user_group_id
            ];
        }
        $price = Price::find()->where($params)->one();
        return $price;
    }

    /**
     * @param int $userGroupId
     * @return array|null|ActiveRecord
     * @throws Exception
     */
    public function getPriceByUserGroup(int $userGroupId) {
        if (!empty($userGroupId)) {
            $price = Price::find()->where([
                'user_group_id' => $userGroupId
            ])->one();
            return $price;
        }
        else throw new Exception('User group id is empty');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombinationAttributes()
    {
        return $this->hasMany(CombinationAttribute::className(), ['combination_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(CombinationImage::className(), ['combination_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopCombinationTranslations()
    {
        return $this->hasMany(CombinationTranslation::className(), ['combination_id' => 'id']);
    }

    /**
     * Finds default combination for one product and makes it not default.
     */
    public function findDefaultCombinationAndUndefault() {
        $defaultProductCombination = Combination::find()
            ->where(['product_id' => $this->product_id, 'default' => true])
            ->andWhere(['!=', 'id', $this->id])->one();
        if (!empty($defaultProductCombination)) {
            $defaultProductCombination->default = 0;
            if ($defaultProductCombination->validate()) $defaultProductCombination->save();
        }
    }

    /**
     * If there are not combinations in product, sets this combination as default.
     */
    public function setDefaultOrNotDefault() {
        if ($this->default) $this->findDefaultCombinationAndUndefault();
        else {
            $productCombinations = Combination::find()
                ->where(['product_id' => $this->product_id])->all();
            if (empty($productCombinations)) $this->default = true;
        }
    }

}