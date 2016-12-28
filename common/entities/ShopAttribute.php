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
     * Attribute types
     */
    const TYPE_DROP_DOWN_LIST = 1;
    const TYPE_RADIO_BUTTON = 2;
    const TYPE_COLOR = 3;
    const TYPE_TEXTURE = 4;

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
            [['type_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
    public function getAttributeValues()
    {
        return $this->hasMany(ShopAttributeValue::className(), ['attribute_id' => 'id']);
    }

    /**
     * @param $combinationsIds
     * @return ProductCombinationAttribute[]
     */
    public function getProductCombinationAttributes($combinationsIds) {
        $productCombinationAttributes = ProductCombinationAttribute::find()->where(['attribute_id' => $this->id])
            ->andWhere(['combination_id' => $combinationsIds])->all();
        return $productCombinationAttributes;
    }
}
