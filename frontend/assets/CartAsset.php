<?php
namespace bl\cms\shop\frontend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class CartAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/frontend/web';

    public $css = [
        'css/cart.css',
    ];

    public $js = [
    ];

    public $depends = [
    ];
}