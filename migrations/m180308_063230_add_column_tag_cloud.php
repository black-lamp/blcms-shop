<?php

use yii\db\Migration;

/**
 * Class m180308_063230_add_column_tag_cloud
 */
class m180308_063230_add_column_tag_cloud extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_category', 'tag_cloud', $this->boolean());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_category', 'tag_cloud');
        return true;
    }
}
