<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $product_id
 * @property integer $vendor_id
 * @property string $image_name
 *
 * @property Category $category
 * @property Vendor $vendor
 * @property Param[] $params
 * @property ProductPrice[] $prices
 * @property ProductTranslation[] $translations
 * @property ProductTranslation $translation
 */
class Product extends ActiveRecord
{
    public $imageFile;

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
        ];
    }

    public function rules()
    {
        return [
            [['position', 'category_id', 'vendor_id', 'country_id'], 'number'],
            [['price'], 'number'],
            [['imageFile'], 'file']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
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
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
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

    // TODO: remove this method
    public function getImageSrc($type) {
        if(!empty($this->image_name)) {
            return '/images/shop/' . $this->image_name . '-' . $type . '.jpg';
        }

        return null;
    }

    public function getThumbImage() {
        return $this->getImageSrc('thumb');
    }

    public function getOriginalImage() {
        return $this->getImageSrc('original');
    }

    public function getBigImage() {
        return $this->getImageSrc('big');
    }

    public static function generateImageName($baseName) {
        $fileName = hash('crc32', $baseName . time());
        if(file_exists(Yii::getAlias('@frontend/web/images/shop/' . $fileName . '-original.jpg'))) {
            return static::generateImageName($baseName);
        }
        return $fileName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::className(), ['product_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(ProductVideo::className(), ['product_id' => 'id']);
    }
}
