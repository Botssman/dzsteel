<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * ontarget_catalog_order_customer Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('ontarget_catalog_order_customer', function(Blueprint $table) {
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->primary(['order_id', 'customer_id']);
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('ontarget_catalog_order_customer');
    }
};
