<?php
namespace bl\cms\shop\frontend\models;
use bl\cms\shop\common\entities\Category;
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

    public $params = [];

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

    /**
     * @param Category $category
     * @param null|string $data
     * @param null $formName
     * @return bool
     */
    public function loadFromCategory($category, $data, $formName = null)
    {
        if(!empty($category->filter)) {
            foreach ($category->filter->params as $filterParam) {
                $this->params[$filterParam->key] = $data[$filterParam->key];
            }
        }
        return parent::load($data, $formName);
    }


}