<?php
/**
 * Created by xalbert.einsteinx
 * https://www.einsteinium.pro
 * Date: 20.05.2016
 * Time: 11:48
 */

namespace bl\cms\multishop\common\entities;

use bl\multilang\entities\Language;
use bl\seo\behaviors\SeoDataBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class CategoryTranslation extends ActiveRecord
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
            [['category_id', 'language_id'], 'number'],
            [['title', 'description'], 'string'],
            // seo data
//            [['seoUrl', 'seoTitle', 'seoDescription', 'seoKeywords'], 'string']
        ];
    }
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'text' => 'Text',
        ];
    }
    
    public static function tableName() {
        return 'shop_category_translation';
    }
    
    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    
    public function getLanguage() {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

}