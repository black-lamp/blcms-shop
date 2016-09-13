<?php

namespace bl\cms\shop\common\entities;

use Yii;

/**
 * This is the model class for table "shop_filters".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $filter_by_vendor
 * @property integer $filter_by_country
 *
 * @property Category $category
 */
class Filter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_filters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'filter_by_vendor', 'filter_by_country'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'category_id' => Yii::t('shop', 'Category ID'),
            'filter_by_vendor' => Yii::t('shop', 'Filter by vendor'),
            'filter_by_country' => Yii::t('shop', 'Filter by country'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}