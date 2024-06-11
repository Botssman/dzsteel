<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ontarget_catalog_properties', function(Blueprint $table){
            $table->boolean('show_in_filters')->default(false);
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_properties', function(Blueprint $table){
            $table->dropColumn('show_in_filters');
        });
    }
};
