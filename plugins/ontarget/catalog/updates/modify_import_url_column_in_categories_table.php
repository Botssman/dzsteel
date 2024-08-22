<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ontarget_catalog_categories', function(Blueprint $table){
            $table->text('import_url')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_categories', function(Blueprint $table){
            $table->string('import_url')->nullable()->change();
        });
    }
};
