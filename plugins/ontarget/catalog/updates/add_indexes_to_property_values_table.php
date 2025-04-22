<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {

        Schema::table('ontarget_catalog_property_values', function(Blueprint $table){
            //$table->index('slug');
            //$table->index('name');
            //$table->unique(['property_id', 'name']);
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_property_values', function(Blueprint $table){
            //$table->dropUnique(['property_id', 'name']);
            //$table->dropIndex(['slug']);
            //$table->dropIndex(['name']);
        });
    }
};
