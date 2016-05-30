<?php

namespace bl\cms\shop\common\entities;

use bl\cms\shop\common\entities\Product;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_vendor".
 *
 * @property integer $id
 * @property string $title
 *
 * @property Product[] $products
 */
class Vendor extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_vendor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('blcms-shop/backend/vendor', 'ID'),
            'title' => Yii::t('blcms-shop/backend/vendor', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['vendor_id' => 'id']);
    }
}
