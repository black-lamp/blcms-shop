<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class EditProductAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/backend/web';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/title-generation.js'
    ];

    public $depends = [
        'bl\cms\shop\backend\assets\ProductAsset',
        'bl\cms\shop\backend\assets\InputTreeAsset'
    ];
}