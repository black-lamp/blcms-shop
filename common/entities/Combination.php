<?php
namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use bl\cms\shop\common\components\user\models\UserGroup;
use yii\db\Expression;

/**
 * This is the model class for table "shop_combination".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $sku
 * @property integer $default
 * @property string $creation_time
 * @property string $update_time
 *
 * @property Product $product
 * @property CombinationAttribute[] $combinationAttributes
 * @property CombinationImage[] $images
 * @property CombinationPrice $price
 * @property CombinationPrice[] $prices
 * @property CombinationTranslation[] $translations
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
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['creation_time', 'update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
                'value' => new Expression('NOW()'),
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
            [['creation_time', 'update_time'], 'safe'],
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
     * @return \yii\db\ActiveQuery[]|Price[]
     */
    public function getPrices()
    {
        $prices = Price::find()->joinWith('combinationPrice')->where(['combination_id' => $this->id])->all();
        return $prices;
    }

    /**
     * Gets price for user group
     * @return array|null|ActiveRecord|Price
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
        $combinationPrice = CombinationPrice::find()->where($params)->one();
        return $combinationPrice->price;
    }

    /**
     * @param int $userGroupId
     * @return bool|mixed
     * @throws Exception
     */
    public function getPriceByUserGroup(int $userGroupId) {
        if (!empty($userGroupId)) {
            $combinationPrice = CombinationPrice::find()
                ->where(['combination_id' => $this->id, 'user_group_id' => $userGroupId])
                ->one();
            if (!empty($combinationPrice)) return $combinationPrice->price;
            else return false;
        }
        else throw new Exception('User group id is empty');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombinationPrices() {
        return $this->hasMany(CombinationPrice::className(), ['combination_id' => 'id']);
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

    public function getImagesArray() {
        $images = [];

        foreach ($this->images as $image) {
            $images[] = [
                'thumb' => $image->productImage->thumb,
                'small' => $image->productImage->small,
                'big' => $image->productImage->big
            ];
        }

        return $images;
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