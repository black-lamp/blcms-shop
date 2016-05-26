<?php
/**
 * Created by xalbert.einsteinx
 */

namespace bl\cms\shop\common\entities;

/**
 * Product
 * @property integer $id
 * @property integer $category_id
 * @property string $price
 *
 * @property Category $category
 * @property ProductTranslation[] $translations
 * @property ProductTranslation $translation
 */
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ProductTranslation::className(),
                'relationColumn' => 'product_id'
            ],
        ];
    }

    public function rules()
    {
        return [
            ['category_id', 'number'],
            ['price', 'string'],
            [['imageFile'], 'file']
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('upload/shop-images/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
    }
}
