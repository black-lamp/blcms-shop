<?php

namespace bl\cms\shop\backend\components\form;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use bl\imagable\Imagable;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class ProductImageForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $cover;
    /**
     * @var UploadedFile[]
     */
    public $thumbnail;
    /**
     * @var UploadedFile[]
     */
    public $menu_item;

    public function rules()
    {
        return [
            [['cover', 'thumbnail', 'menu_item'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $dir = Yii::getAlias('@frontend/web' . $this->getDirectory());

            $imagable = \Yii::$app->imagable;
            $imagable->imagesPath = $dir;
            $image_name = [];

            /** @var Imagable $this */
            if (!empty($this->cover)) {
                $this->cover->saveAs($dir . $this->cover->baseName . '.jpg');
                $image_name['cover'] = $imagable->create('cover', $dir . $this->cover->baseName . '.jpg');
                unlink($dir . $this->cover->baseName . '.jpg');
            }

            /** @var Imagable $this */
            if (!empty($this->thumbnail)) {
                $this->thumbnail->saveAs($dir . $this->thumbnail->baseName . '.jpg');
                $image_name['thumbnail'] = $imagable->create('thumbnail', $dir . $this->thumbnail->baseName . '.jpg');
                unlink($dir . $this->thumbnail->baseName . '.jpg');
            }

            /** @var Imagable $this */
            if (!empty($this->menu_item)) {
                $this->menu_item->saveAs($dir . $this->menu_item->baseName . '.jpg');
                $image_name['menu_item'] = $imagable->create('menu_item', $dir . $this->menu_item->baseName . '.jpg');
                unlink($dir . $this->menu_item->baseName . '.jpg');
            }
            return $image_name;
        } else {
            return false;
        }
    }

    private function getDirectory() {
        return $dir = '/images/shop-product';
    }
}