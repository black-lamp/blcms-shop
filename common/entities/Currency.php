<?php

namespace bl\cms\shop\common\entities;

use Yii;

/**
 * This is the model class for table "shop_currency".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $value
 * @property string $date
 */


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Currency extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['value'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_currency';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'value' => Yii::t('shop', 'Value'),
            'date' => Yii::t('shop', 'Date'),
        ];
    }

    /**
     * This method return last currency value
     *
     * @return integer
     */
    public static function currentCurrency() {
        $currency = self::find()->orderBy('date DESC')->limit(1)->all();
        return $currency[0]->value;
    }
}
