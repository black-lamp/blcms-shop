<?php

namespace bl\cms\shop\common\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "shop_attribute_value_color_texture".
 *
 * @property integer $id
 * @property string $color
 * @property string $texture
 * 
 * @property ShopAttributeValueTranslation $translation
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

    public static function getTexture($id) {
        return Html::img('/images/shop/attribute-texture/' . self::findOne($id)->texture, ['height' => '100']);
    }
}
