<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\entities\Language;
use Yii;

/**
 * This is the model class for table "shop_attribute_type_translation".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $title
 * @property integer $language_id
 * @property integer $attr_type_id
 *
 * @property Language $language
 * @property ShopAttributeType $type
 */
class ShopAttributeTypeTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute_type_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'language_id', 'attr_type_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopAttributeType::className(), 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'type_id' => Yii::t('shop', 'Type ID'),
            'title' => Yii::t('shop', 'Title'),
            'language_id' => Yii::t('shop', 'Language ID'),
            'attr_type_id' => Yii::t('shop', 'Attr Type ID'),
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
    public function getType()
    {
        return $this->hasOne(ShopAttributeType::className(), ['id' => 'type_id']);
    }
}
