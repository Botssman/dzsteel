<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ontarget_catalog_categories', function (Blueprint $table) {
            $table->index(['name'], 'categories_name_idx');
            $table->index(['slug'], 'categories_slug_idx');
        });

        Schema::table('ontarget_catalog_products', function (Blueprint $table) {
            $table->index(['category_id'], 'products_category_id_idx');
            $table->index(['name'], 'products_name_idx');
            $table->index(['slug'], 'products_slug_idx');
            $table->index(['vendor_code'], 'products_vendor_code_idx');
            $table->index(['category_id', 'name'], 'products_category_name_idx');

        });
    }

    public function down()
    {

    }
};
