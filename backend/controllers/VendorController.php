<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\Vendor;
use yii\web\Controller;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class VendorController extends Controller
{
    public function actionIndex() {
        return $this->render('list', [
            'vendors' => Vendor::find()->all()
        ]);
    }

    public function actionSave($id = null) {
        $vendor = new Vendor();

        if(!empty($id)) {
            $vendor = Vendor::findOne($id);
        }

        if(\Yii::$app->getRequest()->isPost) {
            if($vendor->load(\Yii::$app->request->post())) {
                if($vendor->save()) {
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('save', [
            'vendor' => $vendor
        ]);
    }
}