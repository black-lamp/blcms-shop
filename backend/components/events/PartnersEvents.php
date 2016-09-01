<?php
namespace bl\cms\shop\backend\components\events;
use bl\cms\shop\backend\controllers\PartnersController;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class PartnersEvents implements BootstrapInterface
{

    public function apply() {

    }

    public function decline() {

    }

    public function bootstrap($app)
    {
        Event::on(PartnersController::className(), PartnersController::EVENT_APPLY, 'apply');
        Event::on(PartnersController::className(), PartnersController::EVENT_DECLINE, 'decline');
    }
}