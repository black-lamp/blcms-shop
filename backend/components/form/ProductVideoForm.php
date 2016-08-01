<?php

namespace bl\cms\shop\backend\components\form;
use Yii;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class ProductVideoForm extends Model
{

    public $file;
    public $resource;

    public function rules()
    {
        return [
            [['file', 'resource'], 'string', 'skipOnEmpty' => true]
        ];
    }

    public function upload()
    {
//        if ($this->validate()) {
//            $dir = Yii::getAlias('@frontend/web/images/');
//
//            $imagable = \Yii::$app->imagable;
//            $imagable->imagesPath = $dir;
//
//            /** @var Imagable $this */
//            if (!empty($this->image)) {
//                $this->image->saveAs($dir . $this->image->baseName . '.jpg');
////                die(Yii::getAlias('@frontend/web/images/') . $this->image->baseName . '.jpg');
//                $image_name = $imagable->create('shop-product', Yii::getAlias('@frontend/web/images/') . $this->image->baseName . '.jpg');
//
//                unlink($dir . $this->image->baseName . '.jpg');
//                return $image_name;
//            }
//        }
        return false;
    }
    
}