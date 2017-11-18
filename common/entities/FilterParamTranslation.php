<?php

namespace bl\cms\shop\common\entities;

use bl\multilang\entities\Language;
use Yii;

/**
 * This is the model class for table "shop_product_filter_param_translation".
 *
 * @property string $id
 * @property integer $filter_param_id
 * @property integer $language_id
 * @property string $title
 * @property string $param_name
 *
 * @property FilterParam $filterParam
 */
class FilterParamTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_filter_param_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id'], 'integer'],
            [['filter_param_id'], 'integer'],
            [['id', 'title', 'param_name'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['filter_param_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterParam::className(), 'targetAttribute' => ['filter_param_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend.shop.filter.param.translation', 'ID'),
            'filter_param_id' => Yii::t('backend.shop.filter.param.translation', 'Filter Param ID'),
            'language_id' => Yii::t('backend.shop.filter.param.translation', 'Language ID'),
            'title' => Yii::t('backend.shop.filter.param.translation', 'Title'),
            'param_name' => Yii::t('backend.shop.filter.param.translation', 'Param Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilterParam()
    {
        return $this->hasOne(FilterParam::className(), ['id' => 'filter_param_id'])->inverseOf('filterParamTranslations');
    }
}
