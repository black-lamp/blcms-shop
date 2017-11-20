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
        $js = <<< JS
            $(function() {
                $('.shop-filter .toggle-cover .show').click(function(e) {
                    e.preventDefault();
                    var block = $(this).parent().parent().find('.covered');
                    block.removeClass('covered');
                    block.addClass('uncovered');
                });            
                $('.shop-filter .toggle-cover .hide').click(function(e) {
                    e.preventDefault();
                    var block = $(this).parent().parent().find('.uncovered');
                    block.removeClass('uncovered');
                    block.addClass('covered');
                });            
            });
JS;
        $this->getView()->registerJs($js, View::POS_END);
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