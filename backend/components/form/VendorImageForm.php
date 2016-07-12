<?php
/**
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 */

namespace bl\cms\shop\backend\components\form;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use bl\imagable\Imagable;

class VendorImageForm extends Model
{
    /** @var UploadedFile */
    public $imageFile;

    private $dir;
    private $orig_image_name;
    private $image_name;
    private $image_extension = '.jpg';
    private $category = 'shop-vendors';

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png']
        ];
    }

    public function getImageName() {
        return $this->image_name;
    }

    public function notEmpty()
    {
        return (!empty($this->imageFile));
    }

    public function Upload()
    {
        $this->dir = Yii::getAlias('@frontend/web/images/');

        /** @var Imagable $imagable */
        $imagable = \Yii::$app->imagable;
        $imagable->imagesPath = $this->dir;

        // TODO(maybe): image extensions ($this->imageFile->extension)
        $this->orig_image_name = $this->imageFile->baseName . $this->image_extension;
        $this->imageFile->saveAs($this->dir . $this->orig_image_name);

        $this->image_name = $imagable->create($this->category, $this->dir . $this->orig_image_name);
        unlink($this->dir . $this->orig_image_name);
    }

    public function getBig($image_name) {
        return ('/images/' . $this->category . '/' . $image_name . '-big' . $this->image_extension);
    }

    public function getThumb($image_name) {
        return ('/images/' . $this->category . '/' . $image_name . '-thumb' . $this->image_extension);
    }

    public function getSmall($image_name) {
        return ('/images/' . $this->category . '/' . $image_name . '-small' . $this->image_extension);
    }
}