<?php
namespace bl\cms\shop\backend\events;

use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductEvent extends Event
{

    /**
     * @var integer
     */
    public $productId;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $time;
}