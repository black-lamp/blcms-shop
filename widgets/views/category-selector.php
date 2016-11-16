<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

use bl\cms\shop\widgets\CategorySelector;
?>


<?= '<ul class="list-group ul-treefree ul-dropfree">'; ?>
<?= '<li class="list-group-item"><input type="radio" checked name="' . $inputName . '" value="" id="null"><label for="null">' . \Yii::t("shop", "Without parent") . '</label>'; ?>
<?= CategorySelector::treeRecoursion($categoriesTree, $parentId, $inputName, null); ?>
<?= '</ul>'; ?>