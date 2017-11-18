<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;

/**
 * This is the model class for table "shop_product_filter_param_values".
 *
 * @property integer $id
 * @property integer $filter_param_id
 * @property integer $position
 *
 * @property FilterParam $filterParam
 */
class FilterParamValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_filter_param_values';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => FilterParamValueTranslation::className(),
                'relationColumn' => 'filter_param_value_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_param_id', 'position'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['filter_param_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterParam::className(), 'targetAttribute' => ['filter_param_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend.shop.filter', 'ID'),
            'filter_param_id' => Yii::t('backend.shop.filter', 'Filter Param ID'),
            'value' => Yii::t('backend.shop.filter', 'Value'),
            'position' => Yii::t('backend.shop.filter', 'Position'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilterParam()
    {
        return $this->hasOne(FilterParam::className(), ['id' => 'filter_param_id'])->inverseOf('filterParamValues');
    }
}
