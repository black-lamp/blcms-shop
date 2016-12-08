<?php
namespace bl\cms\shop\common\entities;

use bl\imagable\helpers\FileHelper;
use bl\multilang\behaviors\TranslationBehavior;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "shop_product_image".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $position
 * @property string $file_name
 * @property string $extension
 *
 * @property Product $product
 * @property ProductImageTranslation $translation
 */

class ProductImage extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_image';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductImageTranslation::className(),
                'relationColumn' => 'image_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'product_id'
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'position'], 'integer'],
            [['file_name'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize'=>'3000000'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
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
     */
    public function removeImage() {
        $this->delete();
        \Yii::$app->shop_imagable->delete('shop-product', $this->file_name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductImageTranslation::className(), ['image_id' => 'id']);
    }

    public function getBig() {
        $image = $this->getImage('big');
        return '/images/shop-product/' . FileHelper::getFullName($image);
    }

    public function getThumb() {
        $image = $this->getImage('thumb');
        return '/images/shop-product/' . FileHelper::getFullName($image);
    }

    public function getSmall() {
        $image = $this->getImage('small');
        return '/images/shop-product/' . FileHelper::getFullName($image);
    }

    private function getImage($size) {
        return \Yii::$app->shop_imagable->get('shop-product', $size, $this->file_name);
    }
}
