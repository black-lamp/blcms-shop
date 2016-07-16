<?php
/**
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 */

namespace bl\cms\shop\backend\components\form;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use bl\imagable\Imagable;

class VendorImage extends Model
{
    /** @var UploadedFile */
    public $imageFile;

    private $_dir;
    private $_orig_image_name;
    private $_image_name;
    private $_image_extension = '.jpg';
    private $_category = 'shop-vendors';

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png']
        ];
    }

    public function attributeLabels()
    {
        return [
            'imageFile' => Yii::t('shop', 'Upload image')
        ];
    }

    public function getImageName() {
        return $this->_image_name;
    }

    public function notEmpty()
    {
        return (!empty($this->imageFile));
    }

    public function Upload()
    {
        $this->_dir = Yii::getAlias('@frontend/web/images/');

        /** @var Imagable $imagable */
        $imagable = \Yii::$app->imagable;
        $imagable->imagesPath = $this->_dir;

        // save original
        $this->_orig_image_name = $this->imageFile->baseName . $this->_image_extension;
        $this->imageFile->saveAs($this->_dir . $this->_orig_image_name);
        // create small, thumb & big
        $this->_image_name = $imagable->create($this->_category, $this->_dir . $this->_orig_image_name);
        // delete original
        unlink($this->_dir . $this->_orig_image_name);
    }

    // TODO
    public function Remove($image_name)
    {
        if(!empty($image_name)) {
            unlink($this->_dir . $this->getBig($image_name));
            unlink($this->_dir . $this->getThumb($image_name));
            unlink($this->_dir . $this->getSmall($image_name));
        }
    }

    public function getBig($image_name) {
        return ('/images/' . $this->_category . '/' . $image_name . '-big' . $this->_image_extension);
    }

    public function getThumb($image_name) {
        return ('/images/' . $this->_category . '/' . $image_name . '-thumb' . $this->_image_extension);
    }

    public function getSmall($image_name) {
        return ('/images/' . $this->_category . '/' . $image_name . '-small' . $this->_image_extension);
    }
}