<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

namespace bl\cms\shop\backend;


use bl\cms\cart\CartComponent;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\shop\backend\controllers';
    public $defaultRoute = 'shop';
    public $cartConfig;

    public function init()
    {
        parent::init();
        \Yii::$app->set('cart', \Yii::createObject(CartComponent::className(), $this->cartConfig));
    }

}