<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\cart\common\components\user\models\Profile;
use bl\cms\cart\common\components\user\models\RegistrationForm;
use bl\cms\cart\common\components\user\models\User;
use bl\cms\seo\StaticPageBehavior;
use bl\cms\shop\frontend\components\events\PartnersEvents;
use bl\cms\shop\common\entities\PartnerRequest;
use Yii;
use yii\base\Exception;
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
            'staticPage' => [
                'class' => StaticPageBehavior::className(),
                'key' => 'partner'
            ]
        ];
    }

    const EVENT_SEND = 'send';

    public function actionSend()
    {
        $partner = new PartnerRequest();

        if (Yii::$app->user->isGuest) {
            $user = \Yii::createObject(RegistrationForm::className());
            $profile = \Yii::createObject(Profile::className());
        } else {
            $user = User::findOne(Yii::$app->user->id);
            $profile = Profile::findOne($user->id);
        }

        if (Yii::$app->request->isPost) {

            if (Yii::$app->user->isGuest) {
                if ($user->load(\Yii::$app->request->post())) {

                    $profile->user_id = $user->register();

                    if ($profile->load(Yii::$app->request->post())) {
                        if ($profile->validate()) {
                            $profile->save();

                            $partner->load(Yii::$app->request->post());
                            if ($partner->validate()) {

                                $partner->sender_id = Yii::$app->user->id;
                                $partner->save();

                                $this->trigger(self::EVENT_SEND, new PartnersEvents());

                                Yii::$app->getSession()->setFlash('success', \Yii::t('shop', 'Your partner request was successfully sent.'));
                                return $this->redirect(Yii::$app->request->referrer);
                            }
                        }
                    }
                } else throw new Exception('Registration is failed.');
            }
        }

        return $this->render('send',
            [
                'partner' => $partner,
                'user' => $user,
                'profile' => $profile
            ]
        );
    }

}
