<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\common\entities\PartnerRequest;
use Yii;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class PartnerRequestController extends Controller
{
    public function actionSend()
    {
        $partner = new PartnerRequest();

        if (Yii::$app->request->isPost) {
            $partner->load(Yii::$app->request->post());

            if ($partner->validate()) {

                $partner->sender_id = Yii::$app->user->id;
                $partner->save();

                Yii::$app->getSession()->setFlash('success', \Yii::t('shop', 'Your request was successfully sent.'));
            }
        }

        return $this->render('send',
            [
                'partner' => $partner
            ]
        );
    }

}
