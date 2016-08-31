<?php
namespace bl\cms\shop\common\entities;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_product_sale_type".
 *
 * @property integer $id
 * @property string $title
 *
 * @property ProductPrice[] $prices
 */

class SaleType extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_sale_type';
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
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(ProductPrice::className(), ['sale_type_id' => 'id']);
    }
}
