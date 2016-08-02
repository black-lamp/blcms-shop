<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $video_form ProductVideo
 * @var $video_form_upload ProductVideoForm
 */

use bl\cms\shop\backend\components\form\ProductVideoForm;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductVideo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<h1><?= \Yii::t('shop', 'Video'); ?></h1>
<p><?= \Yii::t('shop', 'Upload video or insert from video-services'); ?></p>

<table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
    <thead class="thead-inverse">
    <tr>
        <th class="text-center col-md-2">
            <?= \Yii::t('shop', 'Resource'); ?>
        </th>
        <th class="text-center col-md-4">
            <?= \Yii::t('shop', 'ID'); ?>
        </th>
        <th class="text-center col-md-4">
            <?= \Yii::t('shop', 'Preview'); ?>
        </th>
        <th class="text-center col-md-2">
            <?= \Yii::t('shop', 'Delete'); ?>
        </th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($product->videos as $video) : ?>
        <tr>
            <td class="text-center">
                <?= $video->resource; ?>
            </td>
            <td class="text-center">
                <?= $video->file_name; ?>
            </td>
            <td class="text-center">
            </td>
            <td class="text-center">
                <a href="<?= Url::toRoute(['delete-video', 'id' => $video->id]); ?>"
                   class="media glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<? $addVideoForm = ActiveForm::begin([
    'action' => [
        'product/add-video',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'data-pjax' => true
    ]
]);
?>
<table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
    <tr class="text-center">
        <td class="col-md-2"></td>
        <td class="col-md-4">
            <?= $addVideoForm->field($video_form, 'resource')->dropDownList(
                [
                    'youtube' => 'YouTube',
                    'vimeo' => 'Vimeo'
                ]
            )->label(false); ?>
        </td>
        <td class="col-md-4">
            <?= $addVideoForm->field($video_form, 'file_name')->label(false); ?>
        </td>
        <td class="col-md-2">
            <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
        </td>
    </tr>
</table>
<? $addVideoForm->end(); ?>

<? $uploadVideoForm = ActiveForm::begin([
    'action' => [
        'product/upload-video',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'data-pjax' => true,
        'enctype' => 'multipart/form-data'
    ]
]);
?>
    <table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
        <tr class="text-center">
            <td class="col-md-2"></td>
            <td class="col-md-4">
            </td>
            <td class="col-md-4">
                <?= $uploadVideoForm->field($video_form_upload, 'file_name')->fileInput()->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>
<? $uploadVideoForm->end(); ?>