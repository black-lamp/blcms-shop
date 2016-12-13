<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "shop_attribute".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $relation_category_id
 *
 * @property ShopAttributeType $type
 * @property ShopAttributeValue[] $shopAttributeValues
 * @property ShopAttributeTranslation $translation
 *
 * @method ShopAttributeTranslation getTranslation($languageId = null)
 */
class ShopAttribute extends ActiveRecord
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
                'relationColumn' => 'attr_id'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'relation_category_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['relation_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['relation_category_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopAttributeType::className(), 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'id'),
            'type' => Yii::t('shop', 'Type'),
            'created_at' => Yii::t('shop', 'Created at'),
            'updated_at' => Yii::t('shop', 'Updated at'),
            'relation_category_id' => Yii::t('relation_category_id', 'Category'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ShopAttributeType::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'relation_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValues()
    {
        return $this->hasMany(ShopAttributeValue::className(), ['attribute_id' => 'id']);
    }
}
