<?php
namespace bl\cms\shop\backend\components\events;

use bl\cms\shop\backend\controllers\PriceController;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ShopLogBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PriceController::className(),
            PriceController::EVENT_AFTER_SAVE_PRICE, [$this, 'addLogRecord']);
        Event::on(PriceController::className(),
            PriceController::EVENT_AFTER_DELETE_PRICE, [$this, 'addLogRecord']);
    }

    public function addLogRecord($event)
    {
        $message = "ID: $event->priceId, userId: $event->userName";

        Yii::info($message, $event->name);
    }

}