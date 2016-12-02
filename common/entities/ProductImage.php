<?php
namespace bl\cms\shop\common\entities;

use bl\imagable\helpers\FileHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "shop_product_image".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $position
 * @property string $file_name
 * @property string $extension
 * @property string $alt
 *
 * @property Product $product
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
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
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
            [['alt'], 'string', 'max' => 255],
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

    public function removeImage($id) {
        if(!empty($id)) {
            $image = ProductImage::findOne($id);

            if (\Yii::$app->shop_imagable->delete('shop-product', $image->file_name)) {
                $image->delete();
            }
            else throw new Exception('Files does not exist');
        }
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
