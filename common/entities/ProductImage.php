<?php
namespace bl\cms\shop\common\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_product_image".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $file_name
 * @property string $alt
 *
 * @property Product $product
 */

class ProductImage extends ActiveRecord
{

    public $imageCategory = 'shop-product';
    private $_image_extension = '.jpg';

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
            [['file_name', 'alt'], 'string', 'max' => 255],
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
            $image->deleteAll($id);
            $file_name = $image->file_name;

            $dir = Yii::getAlias('@frontend/web/images/');

            if (!empty($file_name)) {
                unlink($dir . $this->getBig($file_name));
                unlink($dir . $this->getThumb($file_name));
                unlink($dir . $this->getSmall($file_name));
            }

        }
    }

    public function getBig($file_name) {
        return ($this->imageCategory . '/' . $file_name . '-big' . $this->_image_extension);
    }

    public function getThumb($file_name) {
        return ($this->imageCategory . '/' . $file_name . '-thumb' . $this->_image_extension);
    }

    public function getSmall($file_name) {
        return ($this->imageCategory . '/' . $file_name . '-small' . $this->_image_extension);
    }

    public function getOriginal($file_name) {
        return ($this->imageCategory . '/' . $file_name . '-original' . $this->_image_extension);
    }
}
