<?php
namespace bl\cms\shop\console\models\import;
use yii\base\Model;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class CombinationImportModel extends Model
{
    /**
     * @var AttributeValueImportModel[]
     */
    public $attributeValues;

    /**
     * @var string[]
     */
    public $prices;

    /**
     * @var string[]
     */
    public $images;
}