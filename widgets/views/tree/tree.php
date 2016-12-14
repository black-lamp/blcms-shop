<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * This view is used for by TreeWidget
 *
 * @var $categories bl\cms\shop\common\entities\Category
 * @var $currentCategoryId integer
 * @var $level integer
 * @var $upIconClass string
 * @var $downIconClass string
 */

//die(var_dump(\Yii::$app->view->context->id));
//die(var_dump(\Yii::$app->view->context));
?>

<div id="widget-menu" data-current-category-id="<?=$currentCategoryId; ?>">
    <?= $this->render(
        '@vendor/black-lamp/blcms-shop/widgets/views/tree/categories-ajax',
        [
            'categories' => $categories,
            'currentCategoryId' => $currentCategoryId,
            'level' => $level,
            'upIconClass' => $upIconClass,
            'downIconClass' => $downIconClass
        ]);
    ?>
</div>

<?php
$this->registerJs('
    var upIconClass = "' . $upIconClass . '";
    var downIconClass = "' . $downIconClass . '";
', $this::POS_HEAD);
?>

