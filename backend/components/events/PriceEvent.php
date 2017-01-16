<?php
namespace bl\cms\shop\backend\components\events;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class PriceEvent extends Event
{

    /**
     * @var integer
     */
    public $priceId;

    /**
     * @var integer
     */
    public $userName;

}