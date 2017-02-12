<?php
namespace bl\cms\shop\frontend\components\events;
use bl\cms\cart\common\components\user\models\Profile;
use bl\cms\shop\common\entities\PartnerRequest;
use bl\cms\shop\frontend\controllers\PartnerRequestController;
use bl\multilang\entities\Language;
use Exception;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\helpers\Url;

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

            $userMailVars = [
                '{name}' => $profile['name'],
                '{surname}' => $profile['surname'],
                '{patronymic}' => $profile['patronymic'],
                '{info}' => $profile['info'],
            ];
        }
        else {
            $profile = Profile::find()->where(['user_id' => Yii::$app->user->id])->one();
            $partnerRequest = PartnerRequest::find()->where(['sender_id' => Yii::$app->user->id])->one();
            $partnerEmail = $profile->user->email;

            $userMailVars = [
                '{name}' => $profile->name,
                '{surname}' => $profile->surname,
                '{patronymic}' => $profile->patronymic,
                '{info}' => $profile->info,
            ];
        }

        $mailVars = [
            '{contact_person}' => $partnerRequest['contact_person'],
            '{company_name}' => $partnerRequest['company_name'],
            '{website}' => $partnerRequest['website'],
            '{message}' => $partnerRequest['message']
        ];

        $mailVars = array_merge($userMailVars, $mailVars);

        //Message to manager
        $mailTemplate = \Yii::$app->get('emailTemplates')
            ->getTemplate('partner-request-manager', Language::getCurrent()->id);
        $mailTemplate->parseSubject($mailVars);
        $mailTemplate->parseBody($mailVars);
        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

        try {

            \Yii::$app->shopMailer->compose('mail-body', $bodyParams)
                ->setFrom([$event->sender->module->senderEmail => \Yii::$app->name ?? Url::to(['/'], true)])
                ->setTo($event->sender->module->partnerManagerEmail)
                ->setSubject($subject)
                ->send();

        } catch (Exception $ex) {
            throw new Exception($ex);
        }

        //Message to partner
        $mailTemplate = \Yii::$app->get('emailTemplates')
            ->getTemplate('partner-request-partner', Language::getCurrent()->id);
        $mailTemplate->parseSubject($mailVars);
        $mailTemplate->parseBody($mailVars);
        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

        try {

            \Yii::$app->shopMailer->compose('mail-body', $bodyParams)
                ->setFrom([$event->sender->module->senderEmail => \Yii::$app->name ?? Url::to(['/'], true)])
                ->setTo($partnerEmail)
                ->setSubject($subject)
                ->send();

        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

}