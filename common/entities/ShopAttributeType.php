<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_attribute_type".
 *
 * @property integer $id
 *
 * @property ShopAttribute[] $shopAttributes
 */
class ShopAttributeType extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopAttributes()
    {
        return $this->hasMany(ShopAttribute::className(), ['type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopAttributeTypeTranslations()
    {
        return $this->hasMany(ShopAttributeTypeTranslation::className(), ['type_id' => 'id']);
    }
}
