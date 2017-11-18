<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;

/**
 * This is the model class for table "shop_product_filter_params".
 *
 * @property integer $id
 * @property integer $filter_id
 * @property string $key
 * @property integer $is_divided
 * @property integer $all_values
 * @property integer $position
 *
 * @property FilterParamValue[] $shopFilterParamValues
 * @property ProductFilter $filter
 * @property FilterParamTranslation $translation
 */
class FilterParam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_filter_params';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => FilterParamTranslation::className(),
                'relationColumn' => 'filter_param_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_id', 'key'], 'required'],
            [['key'], 'string'],
            [['filter_id', 'is_divided', 'all_values', 'position'], 'integer'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductFilter::className(), 'targetAttribute' => ['filter_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend.shop.filter.param', 'ID'),
            'filter_id' => Yii::t('backend.shop.filter.param', 'Filter ID'),
            'key' => Yii::t('backend.shop.filter.param', 'Key'),
            'is_divided' => Yii::t('backend.shop.filter.param', 'Is Divided'),
            'all_values' => Yii::t('backend.shop.filter.param', 'All Values'),
            'position' => Yii::t('backend.shop.filter.param', 'Position'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopFilterParamValues()
    {
        return $this->hasMany(FilterParamValue::className(), ['filter_param_id' => 'id'])->inverseOf('filterParam');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(ProductFilter::className(), ['id' => 'filter_id'])->inverseOf('params');
    }
}
