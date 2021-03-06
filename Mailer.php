<?php
namespace bl\cms\shop;

use Exception;
use yii\base\Component;
use yii\helpers\{
    Html, Url
};
use bl\multilang\entities\Language;
use bl\emailTemplates\data\Template;
use bl\cms\shop\common\entities\Product;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Mailer extends Component
{

    /**
     * Uses emailTemplates component which parses variables
     * and inserts values to email subject and body
     * @param string $mailKey
     * @param array $mailVars
     * @return mixed
     */
    private function createMailTemplate(string $mailKey, array $mailVars = [])
    {

        /**
         * @var $mailTemplate Template
         */
        $mailTemplate = \Yii::$app->get('emailTemplates')
            ->getTemplate($mailKey, Language::getCurrent()->id);
        if (!empty($mailVars) && !empty($mailTemplate)) {
            $mailTemplate->parseSubject($mailVars);
            $mailTemplate->parseBody($mailVars);
        }

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
    public function sendPartnerRequestToManager(array $mailVars, $sendFrom, $sendTo)
    {

        $mailTemplate = $this->createMailTemplate('partner-request-manager', $mailVars);

        if (!empty($mailTemplate)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $this->sendMessage($sendFrom, $sendTo, $subject, $bodyParams);
        }


    }

    /**
     * Sends mail about partner request to partner
     * @param array $mailVars
     * @param array $sendFrom
     * @param array $sendTo
     */
    public function sendPartnerRequestToPartner(array $mailVars, $sendFrom, $sendTo)
    {

        $mailTemplate = $this->createMailTemplate('partner-request-partner', $mailVars);

        if (!empty($mailTemplate)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $this->sendMessage($sendFrom, $sendTo, $subject, $bodyParams);
        }


    }

    /**
     * Sends mail to partner if his request was approved
     * @param string $partnerEmail
     */
    public function sendPartnerAcceptance(string $partnerEmail)
    {
        $mailTemplate = $this->createMailTemplate('partner-request-accept');

        if (!empty($mailTemplate)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $sendFrom = [\Yii::$app->get('shopMailer')->transport->getUsername() => \Yii::$app->name ?? parse_url(Url::base(true), PHP_URL_HOST)];

            $this->sendMessage($sendFrom, $partnerEmail, $subject, $bodyParams);
        }

    }

    /**
     * If user which has not 'createProductWithoutModeration' permission
     * email to manager and this user will be sent
     * @param Product $product
     */
    public function sendNewProductToManagerAndOwner(Product $product)
    {
        $productOwner = $product->productOwner;
        $mailVars = [
            '{productId}' => $product->id,
            '{title}' => $product->translation->title,
            '{ownerId}' => $productOwner->id,
            '{ownerEmail}' => $productOwner->email,
            '{owner}' => !(empty($productOwner->profile->name . ' ' . $productOwner->profile->surname)) ?
                $productOwner->profile->name . ' ' . $productOwner->profile->surname : $productOwner->profile->info,
            '{link}' => Html::a(
                $product->translation->title,
                Url::toRoute('/shop/product/save?id=' . $product->id . '&languageId=' . Language::getCurrent()->id, true)),

        ];

        //Send mail to manager
        $mailTemplate = $this->createMailTemplate('new-product-to-manager', $mailVars);
        $partnerManagerEmail = \Yii::$app->getModule('shop')->partnerManagerEmail;

        if (!empty($mailTemplate) && !empty($partnerManagerEmail)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $sendFrom = [\Yii::$app->get('shopMailer')->transport->getUsername() => \Yii::$app->name ?? parse_url(Url::base(true), PHP_URL_HOST)];

            $this->sendMessage($sendFrom, $partnerManagerEmail, $subject, $bodyParams);
        }

        //Send mail to partner
        $mailTemplate = $this->createMailTemplate('new-product-to-partner', $mailVars);
        if (!empty($mailTemplate) && !empty($productOwner->email)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $sendFrom = [\Yii::$app->get('shopMailer')->transport->getUsername() => \Yii::$app->name ?? parse_url(Url::base(true), PHP_URL_HOST)];

            $this->sendMessage($sendFrom, $productOwner->email, $subject, $bodyParams);
        }
    }

    /**
     * If a product has successfully passed moderation
     * this email to product owner will be sent
     * @param Product $product
     */
    public function sendAcceptProductToOwner(Product $product)
    {

        $productOwner = $product->productOwner;
        $ownerEmail = $productOwner->email;

        $mailVars = [
            '{title}' => $product->translation->title,
            '{ownerEmail}' => $ownerEmail,
            '{owner}' => !(empty($productOwner->profile->name . ' ' . $productOwner->profile->surname)) ?
                $productOwner->profile->name . ' ' . $productOwner->profile->surname : $productOwner->profile->info,
            '{link}' => Html::a(
                $product->translation->title,
                Url::toRoute('/shop/product/save?id=' . $product->id . '&languageId=' . Language::getCurrent()->id, true)),

        ];
        $mailTemplate = $this->createMailTemplate('accept-product-to-owner', $mailVars);

        if (!empty($mailTemplate)) {
            $subject = $mailTemplate->getSubject();
            $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

            $sendFrom = [\Yii::$app->get('shopMailer')->transport->getUsername() => \Yii::$app->name ?? parse_url(Url::base(true), PHP_URL_HOST)];

            $this->sendMessage($sendFrom, $ownerEmail, $subject, $bodyParams);
        }
    }

}