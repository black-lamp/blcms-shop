<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_attribute_value".
 *
 * @property integer $id
 * @property integer $attribute_id
 * @property string $value
 *
 * @property ShopAttribute $attribute
 * @property ShopAttributeValueTranslation[] $shopAttributeValueTranslations
 */
class ShopAttributeValue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ShopAttributeTranslation::className(),
                'relationColumn' => 'attr_value_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopAttribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopAttributeValueTranslations()
    {
        return $this->hasMany(ShopAttributeValueTranslation::className(), ['value_id' => 'id']);
    }
}
