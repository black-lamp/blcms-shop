<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class PjaxLoaderAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/backend/web';

    public $css = [
        'css/pjax-loader.css'
    ];
    public $js = [
        'js/pjax-loader.js'
    ];

    public $depends = [
        'yii\widgets\PjaxAsset'
    ];
}