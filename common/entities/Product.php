<?php

namespace bl\cms\shop\common\entities;

use bl\cms\cart\models\OrderProduct;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
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
 * @property integer $articulus
 * @property string $creation_time
 * @property string $update_time
 * @property integer $status
 * @property integer $owner
 *
 * @property OrderProduct[] $orderProducts
 * @property Param[] $shopParams
 * @property Category $category
 * @property ProductCountry $country
 * @property Vendor $vendor
 * @property ProductImage[] $ProductImages
 * @property ProductPrice[] $productPrices
 * @property ProductVideo[] $productVideos
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
     * This property is used for the Cart component.
     */
    public $count;

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
            [['position', 'category_id', 'vendor_id', 'country_id', 'articulus', 'owner', 'status'], 'integer'],
            [['price'], 'double'],
            [['creation_time', 'update_time'], 'safe'],
            [['owner'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
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
            'price' => Yii::t('shop', 'Price'),
            'articulus' => Yii::t('shop', 'Articulus'),
            'creation_time' => Yii::t('shop', 'Creation Time'),
            'update_time' => Yii::t('shop', 'Update Time'),
            'owner' => Yii::t('shop', 'Owner'),
            'status' => Yii::t('shop', 'Status'),
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
    public function getPrices()
    {
        return $this->hasMany(ProductPrice::className(), ['product_id' => 'id']);
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
        return $this->hasMany(ProductImage::className(), ['product_id' => 'id']);
    }

    public function getImage() {
        return $this->hasOne(ProductImage::className(), ['product_id' => 'id']);
    }

//    public function getBigImage() {
//        $image = $this->image;
//        $imageUrl = (!empty($image)) ?  '/images/' . ProductImage::$imageCategory . '/' . $image->file_name . '-big' . ProductImage::$image_extension :
//            false;
//        return $imageUrl;
//    }
//
//    public function getThumbImage() {
//        $image = $this->image;
//        $imageUrl = (!empty($image)) ?
//            '/images/' . ProductImage::$imageCategory . '/' . $image->file_name . '-thumb' . ProductImage::$image_extension :
//            false;
//        return $imageUrl;
//    }
//
//    public function getSmallImage() {
//        $image = $this->image;
//        $imageUrl = (!empty($image)) ?
//            '/images/' . ProductImage::$imageCategory . '/' . $image->file_name . '-small' . ProductImage::$image_extension :
//            false;
//
//        return $imageUrl;
//    }
//
//    public function getOriginalImage() {
//        $image = $this->images[0];
//        $imageUrl = (!empty($image)) ?
//            '/images/' . ProductImage::$imageCategory . '/' . $image->file_name . '-original' . ProductImage::$image_extension :
//            false;
//        return $imageUrl;
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(ProductVideo::className(), ['product_id' => 'id']);
    }

    public static function generateImageName($baseName)
    {
        $fileName = hash('crc32', $baseName . time());
        if (file_exists(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-original.jpg'))) {
            return static::generateImageName($baseName);
        }
        return $fileName;
    }

}
