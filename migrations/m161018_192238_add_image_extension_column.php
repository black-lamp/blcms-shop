<?php

use yii\db\Migration;

class m161018_192238_add_image_extension_column extends Migration
{
    public function up()
    {
        $this->addColumn('shop_product_image', 'extension', $this->string());
    }

    public function down()
    {
        $this->dropColumn('shop_product_image', 'extension');
    }
}
