<?php
namespace bl\cms\shop\backend\components\events;

use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\Vendor;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class VendorEvent extends Event
{

    /**
     * @var Vendor
     */
    public $vendor;

}