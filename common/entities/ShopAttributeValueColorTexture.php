<?php

namespace bl\cms\shop\common\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_attribute_value_color_texture".
 *
 * @property integer $id
 * @property string $color
 * @property string $texture
 */
class ShopAttributeValueColorTexture extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute_value_color_texture';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color', 'texture'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'color' => Yii::t('shop', 'Color'),
            'texture' => Yii::t('shop', 'Texture'),
        ];
    }
}
