<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\DeliveryMethod
 * @var $modelTranslation bl\cms\cart\models\DeliveryMethodTranslation
 * @var $languages \bl\multilang\entities\Language[]
 * @var $selectedLanguage \bl\multilang\entities\Language
 */

$this->title = Yii::t('app', 'Delivery method');
?>


<div class="panel panel-default">

    <div class="panel-heading">
        <!-- LANGUAGES -->
        <?php if (count($languages) > 1): ?>
            <div class="dropdown pull-right">
                <button class="btn btn-warning btn-xs m-t-xs m-l-xs dropdown-toggle m-r-xs" type="button"
                        id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true">
                    <?= $selectedLanguage->name ?>
                    <span class="caret"></span>
                </button>
                <?php if (count($languages) > 1): ?>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <?php foreach ($languages as $language): ?>
                            <li>
                                <a href="
                                        <?= Url::to([
                                    'save',
                                    'id' => $model->id,
                                    'languageId' => $language->id]) ?>
                                                ">
                                    <?= $language->name ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= Html::encode($this->title); ?>
        </h5>

    </div>

    <div class="panel-body">

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($modelTranslation, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($modelTranslation, 'description')->textArea(['rows' => 7]) ?>
        <div class="form-group">
            <?= Html::a(Yii::t('shop', 'Close'), Url::toRoute('delivery-method/index'), ['class' => 'btn btn-xs btn-danger pull-right']); ?>
            <?= Html::submitButton(Yii::t('shop', 'Save'), ['class' => 'btn btn-xs btn-primary pull-right m-r-xs']); ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>
