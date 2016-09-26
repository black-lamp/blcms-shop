<?php

namespace bl\cms\shop\frontend;

use bl\cms\cart\CartComponent;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\shop\frontend\controllers';

    public $cartConfig;

    public function init()
    {
        parent::init();
        \Yii::$app->cart = \Yii::createObject(CartComponent::className(), $this->cartConfig);
    }
}
