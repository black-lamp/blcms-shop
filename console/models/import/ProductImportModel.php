<?php
namespace bl\cms\shop\console\models\import;

use yii\base\Model;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class ProductImportModel extends Model
{
    /**
     * @var string
     */
    public $baseSku;
    /**
     * @var string
     */
    public $sku;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $categoryId;
    /**
     * @var string
     */
    public $vendorId;

    /**
     * @var string[]
     */
    public $images;
    /**
     * @var string[]
     */
    public $prices;
    /**
     * @var PropertyImportModel[]
     */
    public $properties;
    /**
     * @var CombinationImportModel[]
     */
    public $combinations;
}