<?php
namespace bl\cms\shop\console\util;

use bl\cms\shop\console\models\import\ProductImportModel;
use Iterator;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
abstract class ProductImportReader implements Iterator
{
    /**
     * ProductImportReader constructor.
     * @param $filename
     */
    public abstract function __construct($filename);


    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return ProductImportModel Can return any type.
     * @since 5.0.0
     */
    public abstract function current();

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public abstract function next();

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public abstract function key();

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public abstract function valid();

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public abstract function rewind();
}