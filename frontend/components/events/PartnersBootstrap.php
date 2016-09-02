<?php
/**
 * Created by PhpStorm.
 * User: xeinsteinx
 * Date: 02.09.16
 * Time: 16:49
 */

namespace bl\cms\shop\frontend\components\events;


use bl\cms\shop\frontend\controllers\PartnerRequestController;
use yii\base\BootstrapInterface;
use yii\base\Event;

class PartnersBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PartnerRequestController::className(), PartnerRequestController::EVENT_SEND, [$this, 'send']);
    }

    public function send($event) {
        die(var_dump($event));
    }

}