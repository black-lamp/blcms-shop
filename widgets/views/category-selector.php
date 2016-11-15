<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

use bl\cms\shop\widgets\CategorySelector;
?>


<?= '<ul class="list-group ul-treefree ul-dropfree">'; ?>
<?= '<li class="list-group-item"><input type="radio" checked name="Category[parent_id]" value="" id="null"><label for="null">' . \Yii::t("shop", "Without parent") . '</label>'; ?>
<?= CategorySelector::treeRecoursion($categoriesTree, $category->parent_id, 'Category[parent_id]', $category_translation->category_id); ?>
<?= '</ul>'; ?>