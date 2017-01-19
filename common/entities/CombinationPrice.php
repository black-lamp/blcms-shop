<?php
namespace bl\cms\shop\common\entities;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "shop_combination_price".
 *
 * @property integer $id
 * @property integer $combination_id
 * @property integer $price_id
 *
 * @property Price $price
 * @property Combination $combination
 */
class CombinationPrice extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_combination_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['combination_id', 'price_id'], 'integer'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['combination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Combination::className(), 'targetAttribute' => ['combination_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('shop', 'ID'),
            'combination_id' => \Yii::t('shop', 'Combination'),
            'price_id' => \Yii::t('shop', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombination()
    {
        return $this->hasOne(Combination::className(), ['id' => 'combination_id']);
    }
}
