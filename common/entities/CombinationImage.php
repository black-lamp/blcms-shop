<?php
namespace bl\cms\shop\common\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_combination_image".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $combination_id
 * @property integer $product_image_id
 *
 * @property Combination $combination
 * @property ProductImage $productImage
 */
class CombinationImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_combination_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['combination_id', 'product_image_id'], 'integer'],
            [['combination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Combination::className(), 'targetAttribute' => ['combination_id' => 'id']],
            [['product_image_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductImage::className(), 'targetAttribute' => ['product_image_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'combination_id' => Yii::t('shop', 'Combination ID'),
            'product_image_id' => Yii::t('shop', 'Product Image ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombination()
    {
        return $this->hasOne(Combination::className(), ['id' => 'combination_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductImage()
    {
        return $this->hasOne(ProductImage::className(), ['id' => 'product_image_id']);
    }
}