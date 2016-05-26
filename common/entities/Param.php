<?php
/**
 * Created by xalbert.einsteinx
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
                'translationClass' => ParamTranslation::className(),
                'relationColumn' => 'param_id'
            ],
        ];
    }

    public function rules()
    {
        return [
            ['product_id', 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_param';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ParamTranslation::className(), ['param_id' => 'id']);
    }
}