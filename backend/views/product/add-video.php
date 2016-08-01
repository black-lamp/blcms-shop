<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product Product
 * @var $video_form ProductVideo
 */

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductVideo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<h1><?= \Yii::t('shop', 'Video'); ?></h1>
<p><?= \Yii::t('shop', 'Upload video or insert from video-services'); ?></p>

<? $addVideoForm = ActiveForm::begin([
    'action' => [
        'product/add-video',
        'productId' => $product->id
    ],
    'method' => 'post',
    'options' => [
        'class' => 'media',
        'data-pjax' => true
    ]
]);
?>
<table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
    <thead class="thead-inverse">
    <tr>
        <th class="text-center col-md-2">
            <?= \Yii::t('shop', 'Resource'); ?>
        </th>
        <th class="text-center col-md-5">
            <?= \Yii::t('shop', 'Link'); ?>
        </th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($product->videos as $video) : ?>
        <tr>
            <td class="text-center">
                <input type="text" disabled="" value="<?= $video->resource; ?>">
            </td>
            <td class="text-center">
                <input type="text" disabled="" value="<?= $video->file_name; ?>">
            </td>
            <td class="text-center">
                <a href="<?= Url::toRoute(['delete-video', 'id' => $video->id]); ?>"
                   class="media glyphicon glyphicon-remove text-danger btn btn-default btn-sm"></a>
            </td>
        </tr>
    <?php endforeach; ?>

    <tr class="text-center">
        <td class="col-md-4">
            <?= $addVideoForm->field($video_form, 'resource')->dropDownList(
                [
                    'youtube' => 'YouTube',
                    'vimeo' => 'Vimeo'
                ]
            )->label(false); ?>
        </td>
        <td class="col-md-6">
            <?= $addVideoForm->field($video_form, 'file_name')->label(false); ?>
        </td>
        <td class="col-md-2">
            <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'media btn btn-primary']) ?>
        </td>
    </tr>
    </tbody>
</table>
<? $addVideoForm->end(); ?>


