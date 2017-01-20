<?php
namespace bl\cms\shop\common\entities;

use bl\cms\cart\models\OrderProduct;
use bl\cms\shop\common\components\user\models\UserGroup;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

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
 * @property CombinationImage[] $images
 * @property CombinationPrice[] $combinationPrice
 * @property CombinationTranslation[] $translations
 * @property OrderProduct[] $orderProducts
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
            [['product_id'], 'integer'],
            [['default'], 'boolean'],
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
     * Gets prices for all user groups
     * @return \yii\db\ActiveQuery
     */
    public function getCombinationPrices()
    {
        $combinationPrices = CombinationPrice::find()->where(['combination_id' => $this->id])->all();
        return $this->hasMany(CombinationPrice::className(), ['combination_id' => 'id']);
    }

    /**
     * Gets price for current user group
     * @return array|null|ActiveRecord
     */
    public function getPrice()
    {
        $user_group_id = (\Yii::$app->user->isGuest) ? UserGroup::USER_GROUP_ALL_USERS :
            \Yii::$app->user->user_group_id;
        $combinationPrice = CombinationPrice::find()->where(['combination_id' => $this->id, 'user_group_id' => $user_group_id])->one();

        return $combinationPrice->price ?? 0;
    }

    /**
     * Gets price for all user groups
     * @return array|null|ActiveRecord
     */
    public function getPrices()
    {
        $combinationPrices = $this->combinationPrices;

        $prices = [];
        foreach ($combinationPrices as $combinationPrice) {
            $prices[] = $combinationPrice->price;
        }
        return $prices;
    }

    /**
     * @param int $userGroupId
     * @return array|null|ActiveRecord
     * @throws Exception
     */
    public function getPriceByUserGroup(int $userGroupId)
    {
        $combinationPrice = CombinationPrice::find()
            ->where(['combination_id' => 'id', 'user_group_id' => $userGroupId])->one();

        return $combinationPrice->price;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
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
        if (!$this->default) {
            $elseCombination = Combination::find()->where(['product_id' => $this->product_id])->one();
            if (empty($elseCombination)) $this->default = true;
        }

        if ($this->default) $this->findDefaultCombinationAndUndefault();
        else {
            $productCombinations = Combination::find()
                ->where(['product_id' => $this->product_id])->all();
            if (empty($productCombinations)) $this->default = true;
        }
    }
}