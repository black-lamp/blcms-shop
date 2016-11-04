<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\frontend\components\events\PartnersEvents;
use bl\cms\shop\common\entities\PartnerRequest;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class PartnerRequestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['send'],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ]
        ];
    }

    const EVENT_SEND = 'send';

    public function actionSend()
    {
        $partner = new PartnerRequest();

        if (Yii::$app->request->isPost) {

            if (!Yii::$app->user->can('productPartner')) {
                $partner->load(Yii::$app->request->post());

                if ($partner->validate()) {

                    $partner->sender_id = Yii::$app->user->id;
                    $partner->save();

                    $this->trigger(self::EVENT_SEND, new PartnersEvents());

                    Yii::$app->getSession()->setFlash('success', \Yii::t('shop', 'Your request was successfully sent.'));
                }
            }
        }

        return $this->render('send',
            [
                'partner' => $partner
            ]
        );
    }

}
