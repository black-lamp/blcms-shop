<?php
namespace bl\cms\shop\backend\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\UploadedFile;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\backend\components\form\VendorImageForm;
use yii\helpers\Url;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class VendorController extends Controller
{
    public function actionIndex() {
        $image_form = new VendorImageForm();
        return $this->render('list', [
            'vendors' => Vendor::find()->all(),
            'image_form' => $image_form
        ]);
    }

    public function actionSave($id = null)
    {
        if(!empty($id)) {
            $vendor = Vendor::findOne($id);
        } else {
            $vendor = new Vendor();
        }

        $image_form = new VendorImageForm();

        if(Yii::$app->getRequest()->isPost) {

            $vendor->load(Yii::$app->request->post());
            $image_form->imageFile = UploadedFile::getInstance($image_form, 'imageFile');

            if($vendor->validate() && $image_form->validate()) {
                if($image_form->notEmpty()) {
                    try {
                        $image_form->Upload();
                        $vendor->image_name = $image_form->getImageName();
                    } catch (Exception $ex) {
                        $vendor->addError('image_file', 'Failed to save image.');
                    }
                }

                $vendor->save();
                Yii::$app->getSession()->setFlash('success', 'All changes has been saved.');
                return $this->redirect(Url::toRoute('/shop/vendor'));
            }
            else {
                Yii::$app->getSession()->setFlash('danger', 'Failed to save changes.');
            }
        }

        return $this->render('save', [
            'vendor' => $vendor,
            'image_form' => $image_form
        ]);
    }
}