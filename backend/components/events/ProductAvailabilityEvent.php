<?php
namespace bl\cms\shop\backend\components\events;

use bl\cms\shop\common\entities\ProductAvailability;
use yii\base\Event;

/**
 * Class ProductAvailabilityEvent
 *
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @package bl\cms\shop\backend\components\events
 */
class ProductAvailabilityEvent extends Event
{
    /**
     * @var ProductAvailability
     */
    public $availability;
}