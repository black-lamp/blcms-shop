<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "shop_product_price_translation".
 *
 * @property integer $id
 * @property integer $price_id
 * @property integer $language_id
 * @property string $title
 *
 * @property Language $language
 * @property ProductPrice $price
 */
class ProductPriceTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_price_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['price_id', 'language_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductPrice::className(), 'targetAttribute' => ['price_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'price_id' => 'Price ID',
            'language_id' => 'Language ID',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(ProductPrice::className(), ['id' => 'price_id']);
    }
}
