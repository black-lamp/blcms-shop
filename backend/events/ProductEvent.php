<?php
namespace bl\cms\shop\backend\events;

use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductEvent extends Event
{
    public function __construct(array $config = null)
    {
        parent::__construct($config);
    }
}