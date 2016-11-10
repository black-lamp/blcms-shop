<?php
namespace bl\cms\shop\frontend\components\events;
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
        try {
            Yii::$app->partnerMailer->compose('partner-request-manager', [
                'partnerRequest' => Yii::$app->request->post('PartnerRequest'),
                'profile' => Yii::$app->request->post('Profile')
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
                'partnerRequest' => Yii::$app->request->post('PartnerRequest'),
            ])
                ->setFrom($event->sender->module->senderEmail)
                ->setTo(Yii::$app->request->post('register-form')['email'])
                ->setSubject(Yii::t('shop', 'Partner request'))
                ->send();
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

}