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
 * @property integer $article_id
 * @property integer $language_id
 * @property integer $product_id
 * @property string $title
 * @property string $description
 * @property string $name
 */

class ProductTranslation extends ActiveRecord
{
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
            [['language_id', 'product_id'], 'number'],
            [['title', 'description', 'characteristics', 'dose'], 'string'],
            [['full_text'], 'string', 'max' => 65536],
            [['seoUrl', 'seoTitle', 'seoDescription', 'seoKeywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_translation';
    }

    public static function getOneProduct($id){
        $model = Category::find()
            ->andWhere(['id' => $id])->one();
        if(empty($model->id))
            return $id;
        return $model;
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
