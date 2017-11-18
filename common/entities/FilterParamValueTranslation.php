<?php

namespace bl\cms\shop\common\entities;

use Yii;

/**
 * This is the model class for table "shop_product_filter_param_value_translation".
 *
 * @property integer $id
 * @property integer $filter_param_value_id
 * @property string $value
 *
 * @property FilterParamValue $filterParamValue
 */
class FilterParamValueTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_filter_param_value_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_param_value_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['filter_param_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterParamValue::className(), 'targetAttribute' => ['filter_param_value_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend.shop.filter', 'ID'),
            'filter_param_value_id' => Yii::t('backend.shop.filter', 'Filter Param Value ID'),
            'value' => Yii::t('backend.shop.filter', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilterParamValue()
    {
        return $this->hasOne(FilterParamValue::className(), ['id' => 'filter_param_value_id'])->inverseOf('filterParamValueTranslations');
    }
}
