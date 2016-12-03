<?php
namespace bl\cms\shop\backend\events;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryEvent extends Event
{

    /**
     * @var integer
     */
    public $categoryId;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $time;
}