<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 21.05.2016
 * Time: 10:37
 */

namespace bl\cms\shop\common\entities;

/**
 * ArticleTranslation
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $language_id
 * @property string $name
 * @property string $text
 * @property string $short_text
 */

use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use bl\seo\entities\SeoData;
use Yii;
use yii\db\ActiveRecord;

class ProductTranslation extends ActiveRecord
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
            [['language_id', 'product_id'], 'number'],
            [['title', 'description'], 'string'],
//            // seo data
//            [['seoUrl', 'seoTitle', 'seoDescription', 'seoKeywords'], 'string']
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
