<?php
namespace bl\cms\shop\common\entities;
use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use bl\seo\entities\SeoData;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $language_id
 * @property string $title
 * @property string $description
 * @property string $full_text
 * @property string $creation_time
 * @property string $update_time
 *
 * @property Language $language
 * @property Product $product
 */

class ProductTranslation extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_product_translation';
    }

    public function behaviors()
    {
        return [
            'seoData' => [
                'class' => SeoDataBehavior::className()
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['creation_time', 'update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['product_id', 'language_id'], 'integer'],
            [['description', 'full_text'], 'string'],
            [['creation_time', 'update_time'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],

            /*SEO params*/
            [['seoUrl', 'seoTitle', 'seoDescription', 'seoKeywords'], 'string'],

            [['seoUrl'], 'uniqueForEntity'],
        ];
    }

    /**
     * Validation rule which checks if such seo url is already exist in current entity
     * @param $attribute
     * @param $params
     */
    public function uniqueForEntity($attribute, $params)
    {
        $seoUrl = SeoData::find()->where(['entity_name' => ProductTranslation::className(), 'seo_url' => $this->seoUrl])->one();
        if (!empty($seoUrl)) $this->addError($attribute, \Yii::t('seo', 'Such url already exists'));
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
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
