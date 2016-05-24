<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 24.05.2016
 * Time: 16:02
 */

namespace bl\cms\shop\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use yii\db\ActiveRecord;

class Param extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => PatamTranslation::className(),
                'relationColumn' => 'product_id'
            ],
        ];
    }

    public function rules()
    {
        return [
            ['category_id', 'number'],
            ['price', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
    }
}