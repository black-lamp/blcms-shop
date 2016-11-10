<?php
namespace bl\cms\shop\frontend\components\events;
use bl\cms\cart\common\components\user\models\Profile;
use bl\cms\shop\common\entities\PartnerRequest;
use bl\cms\shop\frontend\controllers\PartnerRequestController;
use Exception;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class PartnersBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PartnerRequestController::className(), PartnerRequestController::EVENT_SEND, [$this, 'send']);
    }

    public function send($event) {

        if (Yii::$app->user->isGuest) {
            $profile = Yii::$app->request->post('Profile');
            $partnerRequest = Yii::$app->request->post('PartnerRequest');
            $partnerEmail = Yii::$app->request->post('register-form')['email'];
        }
        else {
            $profile = Profile::find()->where(['user_id' => Yii::$app->user->id])->one();
            $partnerRequest = PartnerRequest::find()->where(['sender_id' => $profile->user_id])->one();
            $partnerEmail = $profile->user->email;
        }

        try {
            Yii::$app->partnerMailer->compose('partner-request-manager', [
                'partnerRequest' => $partnerRequest,
                'profile' => $profile
            ])
                ->setFrom($event->sender->module->senderEmail)
                ->setTo($event->sender->module->partnerManagerEmail)
                ->setSubject(Yii::t('shop', 'New partner request'))
                ->send();
        } catch (Exception $ex) {
            throw new Exception($ex);
        }

        try {
            Yii::$app->partnerMailer->compose('partner-request-partner', [
                'partnerRequest' => $partnerRequest,
                'profile' => $profile
            ])
                ->setFrom($event->sender->module->senderEmail)
                ->setTo($partnerEmail)
                ->setSubject(Yii::t('shop', 'Partner request'))
                ->send();
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

}