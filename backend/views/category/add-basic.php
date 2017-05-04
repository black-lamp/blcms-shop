<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $languages[] bl\multilang\entities\Language
 * @var $selectedLanguage bl\multilang\entities\Language
 * @var $maxPosition integer
 * @var $category \bl\cms\shop\common\entities\Category
 * @var $categories \bl\cms\shop\common\entities\Category[]
 * @var $categoryTranslation \bl\cms\shop\common\entities\CategoryTranslation
 */

use marqu3s\summernote\Summernote;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $addForm = ActiveForm::begin([
    'method' => 'post',
    'action' => [
        'category/add-basic',
        'id' => $category->id,
        'languageId' => $selectedLanguage->id
    ],
    'options' => [
        'enctype' => 'multipart/form-data'
    ]]) ?>

<?= Html::submitInput(\Yii::t('shop', 'Save'), ['class' => 'btn btn-xs btn-primary m-r-xs pull-right']); ?>

    <div id="basic">
        <h2><?= \Yii::t('shop', 'Basic options'); ?></h2>

        <!-- NAME -->
        <?= $addForm->field($categoryTranslation, 'title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->label(\Yii::t('shop', 'Name'))
        ?>

        <!-- MENU ITEM TITLE -->
        <?= $addForm->field($categoryTranslation, 'menu_item_title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->label(\Yii::t('shop', 'Menu item title'))
        ?>

        <div class="row">
            <!-- PARENT CATEGORY -->
            <div class="col-md-6">
                <?=
                \bl\cms\shop\widgets\InputTree::widget([
                    'className' => \bl\cms\shop\common\entities\Category::className(),
                    'form' => $addForm,
                    'model' => $category,
                    'attribute' => 'parent_id',
                    'languageId' => $selectedLanguage->id
                ]);
                ?>
            </div>

            <!-- SHOW -->
            <?= $addForm->field($category, 'show', [
                'inputOptions' => [
                    'class' => 'form-control'
                ]
            ])->checkbox(['class' => 'i-checks', 'checked ' => ($category->show) ? '' : false]);
            ?>

            <!-- ADDITIONAL PRODUCTS -->
            <?= $addForm->field($category, 'additional_products', [
                'inputOptions' => [
                    'class' => 'form-control'
                ]
            ])->checkbox(['class' => 'i-checks', 'checked ' => ($category->additional_products) ? '' : false]);
            ?>
        </div>

        <!-- DESCRIPTION -->
        <?= $addForm->field($categoryTranslation, 'description', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->widget(Summernote::className())->label(\Yii::t('shop', 'Description'))
        ?>

        <!-- SORT ORDER -->
        <?= $addForm->field($category, 'position', [
            'inputOptions' => [
                'class' => 'form-control'
            ]])->textInput([
            'type' => 'number',
            'max' => $maxPosition,
            'min' => 1,
            'value' => 1
        ]); ?>

        <div class="ibox">
            <!--CANCEL BUTTON-->
            <a href="<?= Url::to(['/shop/category']); ?>">
                <?= Html::button(\Yii::t('shop', 'Cancel'), [
                    'class' => 'btn btn-danger btn-xs pull-right'
                ]); ?>
            </a>
            <!--SAVE BUTTON-->
            <?= Html::submitInput(\Yii::t('shop', 'Save'), ['class' => 'btn btn-xs btn-primary m-r-xs pull-right']); ?>
        </div>
    </div>
<?php $addForm::end(); ?>