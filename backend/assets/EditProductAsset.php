<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class EditProductAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/backend/web';
    
    public $js = [
        'js/jquery-ui.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'bl\cms\shop\backend\assets\PjaxLoaderAsset'
    ];
}