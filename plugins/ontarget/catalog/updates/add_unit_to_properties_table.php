<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ontarget_catalog_properties', function(Blueprint $table){
            $table->string('unit')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_properties', function(Blueprint $table){
            $table->dropColumn('unit');
        });
    }
};
