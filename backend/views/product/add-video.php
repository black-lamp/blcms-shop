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

    <br>

    <!--ALERT WIDGET-->
<?= \common\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-fade',
    ]]);
?>

    <table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
        <thead class="thead-inverse">
        <tr>
            <th class="text-center" colspan="4">
                <h2><?= \Yii::t('shop', 'Video'); ?></h2>
            </th>
        </tr>
        <?php if (!empty($product->videos)) : ?>
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
        <?php endif; ?>
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
                    <?php if ($video->resource == 'youtube') : ?>
                        <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?= $video->file_name; ?>"
                                frameborder="0" allowfullscreen></iframe>
                    <?php elseif ($video->resource == 'vimeo') : ?>
                        <iframe src="https://player.vimeo.com/video/<?= $video->file_name; ?>" width="100%" height="200"
                                frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    <?php elseif ($video->resource == 'videofile') : ?>
                        <video width="100%" height="200" controls>
                            <source src="/video/<?= $video->file_name; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
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
        'productId' => $product->id,
        'languageId' => $selectedLanguage->id
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
            <td class="col-md-2">
                <strong>
                    <?= \Yii::t('shop', 'Add from service'); ?>
                </strong>
            </td>
            <td class="col-md-4">
                <?= $addVideoForm->field($video_form, 'resource')->dropDownList(
                    [
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo'
                    ]
                )->label(false); ?>
            </td>
            <td class="col-md-4">
                <?= $addVideoForm->field($video_form, 'file_name')->textInput(['placeholder' => \Yii::t('shop', 'Link to video')])->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>

    <table class="col-md-12 table-bordered table-condensed table-stripped table-hover">
        <tr class="text-center">
            <td class="col-md-2">
                <strong>
                    <?= \Yii::t('shop', 'Upload'); ?>
                </strong>
            </td>
            <td class="col-md-4">
            </td>
            <td class="col-md-4">
                <?= $addVideoForm->field($video_form_upload, 'file_name')->fileInput()->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('shop', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>
<? $addVideoForm->end(); ?>