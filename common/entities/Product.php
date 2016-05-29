<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $product_id
 * @property string $image_name
 *
 * @property Category $category
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
        ];
    }

    public function rules()
    {
        return [
            ['category_id', 'number'],
            [['imageFile'], 'file']
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('upload/shop-images/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
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
     * @return integer
     */
    public function getPrice()
    {
        $price = $this->prices[0];
        if($price->type->title == "money") {
            return $price->price - $price->sale;
        }
        else if($price->type->title == "percent") {
            return $price->price - ($price->price / 100) * $price->sale;
        }

        return null;
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
}
