<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

namespace bl\cms\shop\backend\components\form;
use bl\cms\shop\common\entities\ShopAttributeValueColorTexture;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AttributeTextureForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $color;

    public function rules()
    {
        return [
            [['color'], 'string'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        $dir = Yii::getAlias('@frontend/web/images/shop/attribute-texture/');
        if ($this->validate()) {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $fileName = self::generateTextureName();
            $this->imageFile->saveAs($dir . $fileName . '.' . $this->imageFile->extension);
            return $fileName . '.' . $this->imageFile->extension;
        } else {
            return false;
        }
    }

    public static function generateTextureName() {

        $generatedName = uniqid('texture-');
        return $generatedName;
    }

}