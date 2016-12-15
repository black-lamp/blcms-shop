<?php
namespace bl\cms\shop\frontend\widgets\assets;

use yii\web\AssetBundle;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductPricesAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/frontend/widgets/assets/src';

    public $css = [
        'css/combination.css'
    ];
    public $js = [
        'js/combination.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}