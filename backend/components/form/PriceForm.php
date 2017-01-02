<?php
namespace bl\cms\shop\backend\components\form;

use bl\cms\shop\common\entities\PriceDiscountType;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class PriceForm extends Model
{
    public $price;
    public $discount;
    public $discount_type_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount_type_id'], 'integer'],
            [['price', 'discount'], 'double'],
            [['discount_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceDiscountType::className(), 'targetAttribute' => ['discount_type_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('shop', 'ID'),
            'price' => \Yii::t('shop', 'Price'),
            'discount' => \Yii::t('shop', 'Discount'),
            'discount_type_id' => \Yii::t('shop', 'Discount type')
        ];
    }
}