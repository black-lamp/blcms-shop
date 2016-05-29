<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\behaviors\TranslationBehavior;
use yii\db\ActiveRecord;
/**
 * @author Albert Gainutdinov
 *
 * @property integer $product_id
 * @property integer $param_id
 */



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