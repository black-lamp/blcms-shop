<?php
/**
 * Created by PhpStorm.
 * User: xeinsteinx
 * Date: 02.09.16
 * Time: 16:49
 */

namespace bl\cms\shop\backend\components\events;


use bl\cms\cart\common\components\user\models\Profile;
use bl\cms\shop\backend\controllers\PartnersController;
use bl\cms\shop\Mailer;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

class PartnersBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PartnersController::className(), PartnersController::EVENT_APPLY, [$this, 'apply']);
        Event::on(PartnersController::className(), PartnersController::EVENT_DECLINE, [$this, 'decline']);
    }

    public function apply($event) {


        if (Yii::$app->user->isGuest) {
            $partnerEmail = Yii::$app->request->post('register-form')['email'];
        } else {
            $profile = Profile::find()->where(['user_id' => Yii::$app->user->id])->one();
            $partnerEmail = $profile->user->email;
        }

        $mailer = \Yii::createObject(Mailer::className());
        $mailer->sendPartnerAcceptance($partnerEmail);

    }
    public function decline($event) {

    }

}