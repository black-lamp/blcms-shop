<?php
namespace bl\cms\shop\frontend;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\shop\frontend\controllers';

    /**
     * @var string
     * Partner manager e-mail, on which partner request will be sent.
     */
    public $partnerManagerEmail;

    /**
     * @var string
     * From this e-mail partner request will be sent to Partner manager and partner.
     */
    public $senderEmail;

    public function init()
    {
        parent::init();
    }
}
