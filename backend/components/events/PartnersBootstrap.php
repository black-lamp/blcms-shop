<?php
namespace bl\cms\shop\backend\components\events;

use bl\cms\shop\Mailer;
use yii\base\{
    BootstrapInterface, Event
};
use bl\cms\shop\backend\controllers\PartnersController;
use bl\cms\shop\common\components\user\models\User;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class PartnersBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PartnersController::className(), PartnersController::EVENT_APPLY, [$this, 'apply']);
        Event::on(PartnersController::className(), PartnersController::EVENT_DECLINE, [$this, 'decline']);
    }

    public function apply($event)
    {

        $userId = $event->partnerUserId;
        $user = User::findOne($userId);

        if (!empty($user->email)) {
            $mailer = \Yii::createObject(Mailer::className());
            $mailer->sendPartnerAcceptance($user->email);
        }

    }

    public function decline($event)
    {

    }

}