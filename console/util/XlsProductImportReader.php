<?php
namespace bl\cms\shop\console\util;
use bl\cms\shop\console\models\import\AttributeValueImportModel;
use bl\cms\shop\console\models\import\CombinationImportModel;
use bl\cms\shop\console\models\import\ProductImportModel;
use bl\cms\shop\console\models\import\PropertyImportModel;
use PHPExcel_IOFactory;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class XlsProductImportReader extends ProductImportReader
{
    private $sheet;
    private $currentRow;
    private $highestRow;
    private $highestColumn;
    private $startRow;

    private $columns = [
        'baseSku' => 0,
        'sku' => 1,
        'title' => 2,
        'prices' => 3,
        'categoryId' => 4,
        'vendorId' => 5,
        'properties' => 6,
        'combinations' => 7,
        'images' => 8,
    ];

    /**
     * ProductImportReader constructor.
     * @param string $filename
     * @param int $startRow
     */
    public function __construct($filename, $startRow = 2)
    {
        $this->startRow = $startRow;
        $PHPExcel = PHPExcel_IOFactory::load($filename);
        $this->sheet = $PHPExcel->getSheet(0);
        $this->highestRow = $this->sheet->getHighestRow();
        $this->highestColumn = $this->sheet->getHighestColumn();
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return ProductImportModel Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $rowData = $this->sheet->rangeToArray('A' . $this->currentRow . ':' . $this->highestColumn . $this->currentRow, null, true, false)[0];

        $productImportModel = new ProductImportModel([
            'baseSku' => $rowData[$this->columns['baseSku']],
            'sku' => $rowData[$this->columns['sku']],
            'title' => $rowData[$this->columns['title']],
            'categoryId' => $rowData[$this->columns['categoryId']],
            'vendorId' => $rowData[$this->columns['vendorId']],
            'prices' => $this->parsePrices($rowData[$this->columns['prices']]),
            'properties' => $this->parseProperties($rowData[$this->columns['properties']]),
            'combinations' => $this->parseCombinations($rowData[$this->columns['combinations']]),
            'images' => $this->parseImages($rowData[$this->columns['images']]),
        ]);

        return $productImportModel;
    }

    private function parseImages($imagesRowData)
    {
        $images = [];
        foreach (explode("\n", $imagesRowData) as $image) {
            $image = trim($image);
            if (!empty($image)) {
                $images[] = $image;
            }
        }
        return $images;
    }

    private function parsePrices($pricesRowData) {
        $prices = [];
        foreach (explode(";", $pricesRowData) as $price) {
            $price = trim($price);
            if(!empty($price)) {
                $prices[] = $price;
            }
        }
        return $prices;
    }

    private function parseProperties($propertiesRowData)
    {
        $properties = [];

        foreach (explode("\n", $propertiesRowData) as $propertyData) {
            $propertyKeyValue = explode(":", $propertyData);
            if(count($propertyKeyValue) == 2) {
                $title = trim($propertyKeyValue[0]);
                $value = trim($propertyKeyValue[1]);

                if(!empty($title) && !empty($value)) {
                    $properties[] = new PropertyImportModel([
                        'title' => $title,
                        'value' => $value
                    ]);
                }
            }
        }

        return $properties;
    }

    private function parseCombinations($combinationsRowData)
    {
        $combinations = [];

        foreach (explode("\n", $combinationsRowData) as $combinationData) {
            $combinationData = explode(";", $combinationData);
            if(count($combinationData) > 2) {
                $attributeValues = [];
                $prices = [];

                foreach ($combinationData as $attrOrPrice) {
                    $attrOrPrice = trim($attrOrPrice);
                    if(!empty($attrOrPrice)) {
                        $attributeKeyValue = explode(":", $attrOrPrice);
                        if(count($attributeKeyValue) == 2) {
                            $title = trim($attributeKeyValue[0]);
                            $value = trim($attributeKeyValue[1]);

                            if(!empty($title) && !empty($value)) {
                                $attributeValues[] = new AttributeValueImportModel([
                                    'title' => $title,
                                    'value' => $value
                                ]);
                            }
                        } else {
                            $prices[] = $attrOrPrice;
                        }
                    }
                }

                if(!empty($attributeValues) && !empty($prices)) {
                    $combinations[] = new CombinationImportModel([
                        'attributeValues' => $attributeValues,
                        'prices' => $prices
                    ]);
                }
            }
        }

        return $combinations;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->currentRow++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->currentRow;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->currentRow <= $this->highestRow;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->currentRow = $this->startRow;
    }
}