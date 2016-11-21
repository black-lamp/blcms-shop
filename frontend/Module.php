<?php
namespace bl\cms\shop\frontend;
use Yii;

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

    /**
     * @var array
     * If log is enabled, viewed products will save for users.
     */
    public $log = [
        'enabled' => true,
        'maxProducts' => 'all' // Max number of viewed products by one user.
    ];

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['shop'] =
            Yii::$app->i18n->translations['shop'] ??
            [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath'       => '@vendor/black-lamp/blcms-shop/frontend/messages',
        ];
    }
}
