<?php
namespace bl\cms\shop\frontend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class ProductAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/frontend/web';

    public $css = [
        'css/style.css',
        'css/shop.css',
    ];

    public $js = [
        'scripts/script.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}