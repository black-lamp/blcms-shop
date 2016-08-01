<?php

namespace bl\cms\shop\backend\components\form;
use bl\cms\shop\common\entities\Product;
use Yii;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class ProductVideoForm extends Model
{

    public $file_name;

    public function rules()
    {
        return [
            [['file_name'], 'file', 'skipOnEmpty' => false, 'extensions' => 'avi, mp4']
        ];
    }

    public function upload()
    {

        if ($this->validate()) {

            $dir = Yii::getAlias('@frontend/web/video');

            if (!file_exists($dir)) {
                mkdir($dir);
            }
            
            if (!empty($this->file_name)) {

                $baseName = Product::generateImageName($this->file_name->name);

                $this->file_name->saveAs($dir . '/' . $baseName . '.' . end(explode("/", $this->file_name->type)));

                return $baseName;
            }

        }

        return false;
    }
    
}