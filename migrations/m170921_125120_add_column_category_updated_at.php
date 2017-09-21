<?php

use yii\db\Migration;

class m170921_125120_add_column_category_updated_at extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_category', 'updated_at', $this->dateTime());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_category', 'updated_at');
        return true;
    }
}
