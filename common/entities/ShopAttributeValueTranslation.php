<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_attribute_value_translation".
 *
 * @property integer $id
 * @property integer $value_id
 * @property string $title
 * @property integer $language_id
 *
 * @property Language $language
 * @property ShopAttributeValue $value
 */
class ShopAttributeValueTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute_value_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value_id', 'language_id'], 'integer'],
            [['value'], 'string', 'max' => 255],

            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['value_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopAttributeValue::className(), 'targetAttribute' => ['value_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'Id'),
            'value' => Yii::t('shop', 'Value')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasOne(ShopAttributeValue::className(), ['id' => 'value_id']);
    }
}
