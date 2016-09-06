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
 * @property ShopAttributeTypeTranslation[] $shopAttributeTypeTranslations
 */
class ShopAttributeType extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ShopAttributeTypeTranslation::className(),
                'relationColumn' => 'attr_type_id'
            ],
        ];
    }

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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
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
