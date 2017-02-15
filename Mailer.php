<?php
namespace bl\cms\shop;

use bl\emailTemplates\data\Template;
use bl\multilang\entities\Language;
use Exception;
use yii\base\Component;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Mailer extends Component
{

    /**
     * @param string $mailKey
     * @param array $mailVars
     * @return mixed
     */
    private function createMailTemplate(string $mailKey, array $mailVars) {

        /**
         * @var $mailTemplate Template
         */
        $mailTemplate = \Yii::$app->get('emailTemplates')
            ->getTemplate($mailKey, Language::getCurrent()->id);
        $mailTemplate->parseSubject($mailVars);
        $mailTemplate->parseBody($mailVars);

        return $mailTemplate;
    }

    /**
     * @param $sendFrom
     * @param $sendTo
     * @param string $bodySubject
     * @param array $bodyParams
     * @throws Exception
     */
    private function sendMessage($sendFrom, $sendTo, string $bodySubject, array $bodyParams = [])
    {
        if (!empty($sendTo)) {
            try {

                \Yii::$app->get('shopMailer')->compose('mail-body', $bodyParams)
                    ->setFrom($sendFrom)
                    ->setTo($sendTo)
                    ->setSubject($bodySubject)
                    ->send();

            } catch (Exception $ex) {
                throw new Exception($ex);
            }
        }
    }

    /**
     * Sends mail about partner request to manager
     * @param array $mailVars
     * @param $sendFrom
     * @param $sendTo
     */
    public function sendPartnerRequestToManager(array $mailVars, $sendFrom, $sendTo) {

        $mailTemplate = $this->createMailTemplate('partner-request-manager', $mailVars);

        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

        $this->sendMessage($sendFrom, $sendTo, $subject, $bodyParams);

    }

    /**
     * Sends mail about partner request to partner
     * @param array $mailVars
     * @param array $sendFrom
     * @param array $sendTo
     */
    public function sendPartnerRequestToPartner(array $mailVars, $sendFrom, $sendTo) {

        $mailTemplate = $this->createMailTemplate('partner-request-partner', $mailVars);

        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

        $this->sendMessage($sendFrom, $sendTo, $subject, $bodyParams);

    }
}