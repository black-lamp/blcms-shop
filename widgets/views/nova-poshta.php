<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $language string
 * @var $warehouses->data
 * @var $model
 * @var $attribute string
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
?>

<?= Html::activeDropDownList(
    $model,
    $attribute,
    ArrayHelper::map($warehouses->data, 'Number', 'DescriptionRu'),
    [
        'class' => ''
    ]);
?>


