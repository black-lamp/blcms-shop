<?php
namespace bl\cms\shop\frontend\models;
use yii\base\Model;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class FilterModel extends Model
{
    public $maxPrice;
    public $minPrice;

    public $pfrom;
    public $pto;

    public $vendors = [];
    public $availabilities = [];

    public function rules()
    {
        return [
            [['pfrom', 'pto', 'vendors', 'availabilities'], 'safe'],
            [['pfrom', 'pto'], 'number'],
        ];
    }

    public function valuesToArray() {
        return [
            'pfrom' => $this->pfrom,
            'pto' => $this->pto,
            'vendors' => $this->vendors,
            'availabilities' => $this->availabilities,
        ];
    }

}