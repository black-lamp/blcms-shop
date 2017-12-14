<?php
namespace bl\cms\shop\frontend\widgets;
use yii\web\View;
use yii\base\Widget;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class FilterWidget extends Widget
{
    public $shopFilterModel;
    public $vendors;
    public $availabilities;
    public $categoryFilterParams;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('filters', [
            'shopFilterModel' => $this->shopFilterModel,
            'vendors' => $this->vendors,
            'availabilities' => $this->availabilities,
            'categoryFilterParams' => $this->categoryFilterParams,
        ]);
    }
}