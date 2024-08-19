<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ontarget_catalog_categories', function(Blueprint $table){
            $table->string('import_url')->nullable();
            $table->longText('import_settings')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_categories', function(Blueprint $table){
            $table->dropColumn('import_url');
            $table->dropColumn('import_settings');
        });
    }
};
