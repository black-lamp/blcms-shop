<?php
namespace bl\cms\shop\frontend\components\events;
use bl\cms\shop\frontend\controllers\PartnerRequestController;
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
        Event::on(PartnerRequestController::className(), PartnerRequestController::EVENT_SEND, 'apply');
    }
}