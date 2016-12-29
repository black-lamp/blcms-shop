<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\entities\Language;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_combination_translation".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $combination_id
 * @property integer $language_id
 * @property string $description
 *
 * @property Language $language
 * @property Combination $combination
 */
class CombinationTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_combination_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['combination_id', 'language_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['combination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Combination::className(), 'targetAttribute' => ['combination_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('shop', 'ID'),
            'combination_id' => \Yii::t('shop', 'Combination'),
            'language_id' => \Yii::t('shop', 'Language'),
            'description' => \Yii::t('shop', 'Description'),
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
    public function getCombination()
    {
        return $this->hasOne(Combination::className(), ['id' => 'combination_id']);
    }
}