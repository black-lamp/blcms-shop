<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 24.05.2016
 * Time: 15:21
 */

namespace bl\cms\multishop\common\entities;


use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use yii\db\ActiveRecord;

class ParamsTranslation extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'seoData' => [
                'class' => SeoDataBehavior::className()
            ]
        ];
    }

    public function rules()
    {
        return [
            [['param_id'], 'number'],
            [['name', 'value'], 'string'],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_params_translation';
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
    public function getParam()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}