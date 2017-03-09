<?php
namespace bl\cms\shop\backend\components\events;

use bl\cms\shop\common\entities\ShopAttribute;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class AttributeEvent extends Event
{

    /**
     * @var ShopAttribute
     */
    public $attribute;

}