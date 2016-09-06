<?php
/**
 * Created by PhpStorm.
 * User: xeinsteinx
 * Date: 02.09.16
 * Time: 16:49
 */

namespace bl\cms\shop\backend\components\events;


use bl\cms\shop\backend\controllers\PartnersController;
use yii\base\BootstrapInterface;
use yii\base\Event;

class PartnersBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PartnersController::className(), PartnersController::EVENT_APPLY, [$this, 'apply']);
        Event::on(PartnersController::className(), PartnersController::EVENT_DECLINE, [$this, 'decline']);
    }

    public function apply($event) {
        die(var_dump($event));
    }
    public function decline($event) {
        die(var_dump($event));
    }

}