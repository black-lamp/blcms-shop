<?php

namespace bl\cms\shop\widgets\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductFilterAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/frontend/web';

    public $css = [
    ];
    public $js = [
        'scripts/product-filter.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
