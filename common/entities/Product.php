<?php

namespace bl\cms\shop\common\entities;

use bl\cms\cart\common\components\user\models\Profile;
use bl\cms\cart\models\OrderProduct;
use bl\cms\shop\helpers\ShopArrayHelper;
use bl\multilang\behaviors\TranslationBehavior;
use dektrium\user\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\{
    ActiveRecord, Expression
};
use yii\helpers\Url;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "shop_product".
 *
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $position
 * @property integer $category_id
 * @property integer $vendor_id
 * @property integer $country_id
 * @property integer $price
 * @property string $articulus
 * @property string $creation_time
 * @property string $update_time
 * @property integer $status
 * @property integer $owner
 * @property integer $availability
 * @property integer $views
 * @property boolean $sale
 * @property boolean $popular
 *
 * @property OrderProduct[] $orderProducts
 * @property Param[] $params
 * @property Category $category
 * @property ProductCountry $country
 * @property Vendor $vendor
 * @property ProductImage[] $images
 * @property ProductImage $image
 * @property ProductVideo[] $videos
 * @property ProductFile[] $files
 * @property ProductAvailability $productAvailability
 * @property ProductTranslation $translation
 *
 * @method ProductTranslation getTranslation($languageId = null)
 */
class Product extends ActiveRecord
{
    /**
     * Constants for status column
     */
    const STATUS_ON_MODERATION = 1;
    const STATUS_DECLINED = 2;
    const STATUS_SUCCESS = 10;

    /**
     * This properties are used for the Cart component.
     */
    public $count;
    public $combinationIds;
    public $additionalProducts;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'category_id', 'vendor_id', 'country_id', 'owner', 'status', 'owner', 'views'], 'integer'],
            [['sale', 'popular'], 'boolean'],
            [['price'], 'double'],
            [['articulus'], 'string'],
            [['creation_time', 'update_time'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
            [['availability'], 'exist', 'skipOnError' => true, 'targetClass' => ProductAvailability::className(), 'targetAttribute' => ['availability' => 'id']],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductTranslation::className(),
                'relationColumn' => 'product_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'category_id'
                ],
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'position' => Yii::t('shop', 'Position'),
            'price' => Yii::t('shop', 'Base price'),
            'articulus' => Yii::t('shop', 'Articulus'),
            'creation_time' => Yii::t('shop', 'Creation Time'),
            'update_time' => Yii::t('shop', 'Update Time'),
            'owner' => Yii::t('shop', 'Owner'),
            'status' => Yii::t('shop', 'Status'),
            'availability' => Yii::t('shop', 'Availability'),
            'sale' => Yii::t('shop', 'Sale'),
            'popular' => Yii::t('shop', 'Popular'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
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
    public function getVendor()
    {
        return $this->hasOne(Vendor::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductCountry()
    {
        return $this->hasOne(ProductCountry::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
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
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany(Param::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::className(), ['product_id' => 'id'])->orderBy('position');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(ProductImage::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(ProductVideo::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(ProductFile::className(), ['product_id' => 'id']);
    }

    /**
     * Generates unique name by string.
     * @param $baseName
     * @return string
     */
    public static function generateImageName($baseName)
    {
        $fileName = hash('crc32', $baseName . time());
        if (file_exists(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-original.jpg'))) {
            return static::generateImageName($baseName);
        }
        return $fileName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductAvailability()
    {
        return $this->hasOne(ProductAvailability::className(), ['id' => 'availability']);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '/' . Yii::$app->controller->module->id . '/product/show';
        return Url::to([$url, 'id' => $this->id]);
    }

    /**
     * Gets user profile, which created product.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'owner']);
    }

    /**
     * Return true if product has added to favorites already or false if not.
     * @return boolean
     */
    public function isFavorite()
    {
        $favoriteProduct = FavoriteProduct::find()
            ->where(['product_id' => $this->id, 'user_id' => \Yii::$app->user->id])->one();
        if (!empty($favoriteProduct)) return true;
        else return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombinations()
    {
        return $this->hasMany(Combination::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultCombination()
    {
        return $this->hasOne(Combination::className(), ['product_id' => 'id'])
            ->where(['default' => true]);
    }

    /**
     * Gets attributes, which used in product combinations
     * @return ShopAttribute[]
     */
    public function getProductAttributes() {
        $attributes = [];
        foreach ($this->combinations as $combination) {
            foreach ($combination->shopProductCombinationAttributes as $combinationAttribute) {
                $attributes[] = $combinationAttribute->productAttribute;
            }
        }

        $attributes = ShopArrayHelper::removeDuplicatedArrayElements($attributes);

        return $attributes;
    }

}
