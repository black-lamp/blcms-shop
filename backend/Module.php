<?php
namespace bl\cms\shop\backend;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\shop\backend\controllers';
    public $defaultRoute = 'shop';
    public $cartConfig;

    public function init()
    {
        parent::init();
    }

}