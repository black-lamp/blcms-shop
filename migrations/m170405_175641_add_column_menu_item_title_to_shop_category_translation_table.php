<?php

use yii\db\Migration;

class m170405_175641_add_column_menu_item_title_to_shop_category_translation_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_category_translation', 'menu_item_title', $this->string()->after('title'));
    }

    public function down()
    {
        $this->dropColumn('shop_category_translation', 'menu_item_title');
    }
}
