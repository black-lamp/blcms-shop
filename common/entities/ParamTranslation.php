<?php
/**
 * Created by xalbert.einsteinx
 */

namespace bl\cms\shop\common\entities;


use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use yii\db\ActiveRecord;

class ParamTranslation extends ActiveRecord
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
        return 'shop_param_translation';
    }
}