<?php
namespace bl\cms\shop\backend\components\form;

use yii\base\Model;

/**
 * This model is used for uploading files for shop products.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductFileForm extends Model
{

    public $file;
    public $product_id;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize'=>'3000000'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {

            $dir = \Yii::getAlias('@frontend/web/files');

            if (!file_exists($dir)) {
                mkdir($dir);
            }

            if (!empty($this->file)) {

                $baseName = uniqid(hash('crc32', $this->file->name)) . '.' . $this->file->extension;

                $this->file->saveAs($dir . '/' . $baseName);

                return $baseName;
            }
        }
        return false;
    }

}