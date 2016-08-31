<?php
use bl\cms\shop\common\entities\PartnerRequest;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $partner PartnerRequest
 * @var $form ActiveForm
 */

?>

<div class="partner-request-send">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => [
            'partner-request/send',
        ],
        'options' => ['class' => 'tab-content']
    ]);
    ?>

    <?= $form->field($partner, 'company_name') ?>
    <?= $form->field($partner, 'website') ?>
    <?= $form->field($partner, 'message') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
