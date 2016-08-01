<?php

namespace bl\cms\shop\backend\components\form;
use bl\cms\shop\common\entities\Product;
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
    public $image;
    public $link;
    public $alt;

    public function rules()
    {
        return [
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['link', 'alt'], 'string', 'skipOnEmpty' => true]
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $dir = Yii::getAlias('@frontend/web/images/');

            $imagable = \Yii::$app->imagable;
            $imagable->imagesPath = $dir;

            /** @var Imagable $this */
            if (!empty($this->image)) {
                $this->image->saveAs($dir . $this->image->baseName . '.jpg');
                $image_name = $imagable->create('shop-product', Yii::getAlias('@frontend/web/images/') . $this->image->baseName . '.jpg');

                unlink($dir . $this->image->baseName . '.jpg');
                return $image_name;
            }
        }
        return false;
    }

    public function copy($link) {
        $dir = Yii::getAlias('@frontend/web/images/');
        $imagable = \Yii::$app->imagable;
        $imagable->imagesPath = $dir;
        if (exif_imagetype($link) == IMAGETYPE_JPEG || exif_imagetype($link) == IMAGETYPE_PNG) {
            if (!empty($link)) {
                
                $baseName = Product::generateImageName($link);

                $newFile = Yii::getAlias('@frontend/web/images/shop-product/') . $baseName . '.jpg';
                if (copy($link, $newFile)) {
                    $image_name = $imagable->create('shop-product', $newFile);
                    unlink($newFile);
                    return $image_name;
                }
            }
        }
        return false;
    }
}