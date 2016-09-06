<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\entities\Language;
use Yii;

/**
 * This is the model class for table "shop_attribute_value_translation".
 *
 * @property integer $id
 * @property integer $value_id
 * @property string $title
 * @property integer $language_id
 * @property integer $attr_value_id
 *
 * @property Language $language
 * @property ShopAttributeValue $value
 */
class ShopAttributeValueTranslation extends \yii\db\ActiveRecord
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
            [['value_id', 'language_id', 'attr_value_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
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
            'id' => Yii::t('shop', 'ID'),
            'value_id' => Yii::t('shop', 'Value ID'),
            'title' => Yii::t('shop', 'Title'),
            'language_id' => Yii::t('shop', 'Language ID'),
            'attr_value_id' => Yii::t('shop', 'Attr Value ID'),
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
