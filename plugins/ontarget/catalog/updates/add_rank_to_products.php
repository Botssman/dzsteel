<?php namespace OnTarget\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('ontarget_catalog_products', function (Blueprint $table) {
            $table->smallInteger('rank')
                ->default(0)
                ->index();
        });
    }

    public function down()
    {
        Schema::table('ontarget_catalog_products', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
