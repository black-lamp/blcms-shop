<?php
namespace bl\cms\shop\backend;
use Yii;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\shop\backend\controllers';
    public $defaultRoute = 'shop';

    /**
     * @var bool
     * Enables logging in admin panel
     */
    public $enableLog = false;

    /**
     * @var bool
     * It enables the conversion of prices by the currency
     */
    public $enableCurrencyConversion = false;

    /**
     * Enables rounding for prices
     * @var bool
     */
    public $enablePriceRounding = true;

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
                'basePath'       => '@vendor/black-lamp/blcms-shop/backend/messages',
        ];
    }
}