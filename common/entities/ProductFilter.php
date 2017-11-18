<?php

namespace bl\cms\shop\common\entities;

use Yii;

/**
 * This is the model class for table "shop_product_filter".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $show_price_filter
 * @property integer $show_brand_filter
 * @property integer $show_availability_filter
 * @property integer $shop_params_filter
 *
 * @property Category $category
 * @property FilterParam[] $params
 */
class ProductFilter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_filter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'show_price_filter', 'show_brand_filter', 'show_availability_filter', 'shop_params_filter'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend.shop.filter', 'ID'),
            'category_id' => Yii::t('backend.shop.filter', 'Category ID'),
            'show_price_filter' => Yii::t('backend.shop.filter', 'Show Price Filter'),
            'show_brand_filter' => Yii::t('backend.shop.filter', 'Show Brand Filter'),
            'show_availability_filter' => Yii::t('backend.shop.filter', 'Show Availability Filter'),
            'shop_params_filter' => Yii::t('backend.shop.filter', 'Shop Params Filter'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id'])->inverseOf('productFilters');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany(FilterParam::className(), ['filter_id' => 'id'])->inverseOf('filter');
    }
}
