<?php
namespace bl\cms\shop\common\entities;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_product_image".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $file_name
 * @property string $extension
 * @property string $alt
 *
 * @property Product $product
 */

class ProductImage extends ActiveRecord
{

    public static $imageCategory = 'shop-product';

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
    public function rules()
    {
        return [
            [['product_id'], 'integer'],
            [['file_name', 'extension', 'alt'], 'string', 'max' => 255],
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
        return ('/images/' . self::$imageCategory . '/' . $this->file_name . '-big.' . $this->extension);
    }

    public function getThumb() {
        return ('/images/' . self::$imageCategory . '/' . $this->file_name . '-thumb.' . $this->extension);
    }

    public function getSmall() {
        return ('/images/' . self::$imageCategory . '/' . $this->file_name . '-small.' . $this->extension);
    }

    public function getOriginal() {
        return ('/images/' . self::$imageCategory . '/' . $this->file_name . '-original.' . $this->extension);
    }
}
