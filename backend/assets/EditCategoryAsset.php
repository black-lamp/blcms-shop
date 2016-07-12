<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class EditCategoryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/input-tree.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}