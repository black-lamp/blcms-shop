<?php

use yii\db\Migration;

class m171116_164547_create_tables_shop_product_filter extends Migration
{
    public function safeUp()
    {
        $this->createTable('shop_product_filter', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'show_price_filter' => $this->boolean(),
            'show_brand_filter' => $this->boolean(),
            'show_availability_filter' => $this->boolean(),
            'shop_params_filter' => $this->boolean(),
        ]);
        $this->createTable('shop_product_filter_params', [
            'id' => $this->primaryKey(),
            'filter_id' => $this->integer(),
            'key' => $this->string(),
            'is_divided' => $this->boolean(),
            'all_values' => $this->boolean(),
            'position' => $this->integer(),
        ]);
        $this->createTable('shop_product_filter_param_translation', [
            'id' => $this->primaryKey(),
            'filter_param_id' => $this->integer(),
            'language_id' => $this->integer(),
            'title' => $this->string(),
            'param_name' => $this->string(),
        ]);
        $this->createTable('shop_product_filter_param_values', [
            'id' => $this->primaryKey(),
            'filter_param_id' => $this->integer(),
            'position' => $this->integer()
        ]);
        $this->createTable('shop_product_filter_param_value_translation', [
            'id' => $this->primaryKey(),
            'filter_param_value_id' => $this->integer(),
            'language_id' => $this->integer(),
            'value' => $this->string(),
        ]);
        $this->addForeignKey('shop_product_filter_category', 'shop_product_filter', 'category_id', 'shop_category', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_params_filter', 'shop_product_filter_params', 'filter_id', 'shop_product_filter', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_param_translation_filter_param', 'shop_product_filter_param_translation', 'filter_param_id', 'shop_product_filter_params', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_param_translation_language', 'shop_product_filter_param_translation', 'language_id', 'language', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_param_values_filter_param', 'shop_product_filter_param_values', 'filter_param_id', 'shop_product_filter_params', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_param_value_translation_param_value', 'shop_product_filter_param_value_translation', 'filter_param_value_id', 'shop_product_filter_param_values', 'id', 'cascade', 'cascade');
        $this->addForeignKey('shop_product_filter_param_value_translation_language', 'shop_product_filter_param_value_translation', 'language_id', 'language', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('shop_product_filter_category', 'shop_product_filter');
        $this->dropTable('shop_product_filter');
        $this->dropForeignKey('shop_product_filter_params_filter', 'shop_product_filter_params');
        $this->dropTable('shop_product_filter_params');
        $this->dropForeignKey('shop_product_filter_param_translation_filter_param', 'shop_product_filter_param_translation');
        $this->dropTable('shop_product_filter_param_translation');
        $this->dropForeignKey('shop_product_filter_param_values_filter_param', 'shop_product_filter_param_values');
        $this->dropTable('shop_product_filter_param_values');
        $this->dropForeignKey('shop_product_filter_param_value_translation_param_value', 'shop_product_filter_param_value_translation');
        $this->dropTable('shop_product_filter_param_value_translation');
    }
}
