<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * This view is used for by TreeWidget
 *
 * $categories is some object
 */

//die(var_dump(\Yii::$app->view->context->id));
//die(var_dump(\Yii::$app->view->context));
?>

<div id="widget-menu">
    <?= $this->render(
        '@vendor/black-lamp/blcms-shop/widgets/views/categories-ajax',
        [
            'categories' => $categories,
            'level' => $level
        ]);
    ?>
</div>

