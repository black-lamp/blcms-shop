<?php
namespace bl\cms\shop\backend\components\form;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class UploadForm extends Model
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
            $dir = Yii::getAlias('@frontend/web/images/shop-category/');
            $imagable = \Yii::$app->imagable;
            $imagable->imagesPath = Yii::getAlias('@frontend/web/images/shop-category/');
            $image_name = [];
            if (!empty($this->cover)) {
                $this->cover->saveAs($dir . '/' . $this->cover->baseName . '.jpg');
                $image_name['cover'] = $imagable->create('cover', $dir . '/' . $this->cover->baseName . '.jpg');
                unlink($dir.'/' . $this->cover->baseName . '.jpg');
            }
            if (!empty($this->thumbnail)) {
                $this->thumbnail->saveAs($dir . '/' . $this->thumbnail->baseName . '.jpg');
                $image_name['thumbnail'] = $imagable->create('thumbnail', $dir . '/' . $this->thumbnail->baseName . '.jpg');
                unlink($dir.'/' . $this->thumbnail->baseName . '.jpg');
            }
            if (!empty($this->menu_item)) {
                $this->menu_item->saveAs($dir . '/' . $this->menu_item->baseName . '.jpg');
                $image_name['menu_item'] = $imagable->create('menu_item', $dir . '/' . $this->menu_item->baseName . '.jpg');
                unlink($dir.'/' . $this->menu_item->baseName . '.jpg');
            }
            return $image_name;
        } else {
            return false;
        }

//        if ($this->validate()) {
//            $dir = Yii::getAlias('@frontend/web/images/shop-category/' . $type);
//            foreach ($this->$type as $image) {
//                die(var_dump($image));
//                $image->saveAs($dir . '/' . $image->baseName . '.jpg');
//                $imagable = \Yii::$app->imagable;
//                $imagable->imagesPath = Yii::getAlias('@frontend/web/images/shop-category/');
//                $image_name = $imagable->create($type, $dir . '/' . $image->baseName . '.jpg');
//                unlink($dir.'/' . $image->baseName . '.jpg');
//            }
//            return $image_name;
//        } else {
//            return false;
//        }
    }
}